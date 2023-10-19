<?php

declare(strict_types=1);

namespace App\Kernel\AIEngine\Engine;

use App\Annotation\TaskAsync;
use App\Constants\ErrorCode;
use App\Event\AIGenProcessEvent;
use App\Exception\BusinessException;
use App\Kernel\AIEngine\Engine\SDEngine\SDBean;
use App\Library\BaiduTranslate;
use App\Model\ApiAccount;
use App\Model\Message;
use App\Model\SDConfig;
use App\Service\AiOssService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Redis\Redis;
use Throwable;
use App\Library\Bce;
use UUAI\Engine\Annotation\UUAIEngineApiRegister;

#[UUAIEngineApiRegister(
    name       : "基础绘图模型",
    apis       : [
        'ai.image.sd.v1'
    ],
    desc       : '内部服务器搭建',
    engine_name: "sd"
)]
class SDEngine extends BaseEngine
{
    #[Inject]
    protected AiOssService $aiOssService;

    #[Inject]
    protected BaiduTranslate $baiduTranslate;

    #[Inject]
    protected Client $client;

    #[Inject]
    protected ClientFactory $clientFactory;

    #[Inject]
    protected Redis $redis;

    #[Inject]
    protected Bce $bce;

    const SD_MAX_COUNT = 4;
    const DEFAULT_PROMPT         = 'highres,masterpiece,hyper detailed,(best quality,ultra-detailed,8k uhd,ultra high res),high definition,intricate details,';
    const DEFAULT_NEGATIVEPROMPT = 'easynegative,BadDream,ng_deepnegative_v1_75t,badhandv4,NSFW,tattooing,lowres, bad anatomy, bad hands, text, error, missing fingers, extra digit, fewer digits, cropped, worst quality, low quality, normal quality, jpeg artifacts, signature, watermark, username,deformed, ugly, mutilated, disfigured, text, extra limbs, face cut, head cut, extra fingers, extra arms, poorly drawn face, mutation, bad proportions, cropped head, malformed limbs, mutated hands, fused fingers, long neck,drawing,painting,digital art,helmet, nude,nsfw,large breasts,(worst quality:2),(low quality:2),lowres,bad anatomy,bad hands,((monochrome)),((grayscale)),((watermark)),';
    const MODE                   = ['draw', 'image', 'outline', 'qrcode', 'scrawl', 'redraw']; // draw: 绘图(默认)  image: 参考图  outline: 线稿上色  qrcode:图生二维码   scrawl: 涂鸦(简笔画)  redraw: 重绘

    public function gen(): array {
        $message_id   = $this->aiBean->getMessageId();
        $access_token = $this->getApiAccount();
        $prompt       = $this->aiBean->getPrompt();
        $options      = $this->aiBean->getOptions();
        $images       = $this->aiBean->getImages();

        if((isset($options['width']) && $options['width'] > 1200) || (isset($options['height']) && $options['height'] > 1200)) {
            throw new BusinessException(ErrorCode::ERROR_SD_RESOLUTION_ERROR);
        }

        $options['prompt'] = $this->baiduTranslate($prompt);
        try {
            if(!empty($prompt)) {
                $this->bce->text($prompt);
            }
        } catch(\Exception $e) {
            app_event_dispatch(new AIGenProcessEvent($message_id, step: 6, error: '输入内容违规', is_show: 0));
            throw new BusinessException(ErrorCode::ERROR_INTERNAL_REQUEST_BAN);
        }

        $imageBase64 = $this->encodeImage($images[0] ?? '');
        $mode        = $options['mode'] ?? 'draw';
        $debug       = $options['debug'] ?? false;
        if(!in_array($mode, self::MODE)) {
            app_event_dispatch(new AIGenProcessEvent($message_id, step: 6, error: 'mode参数异常', is_show: 0));
            throw new BusinessException(ErrorCode::ERROR_INTERNAL_REQUEST_BAN);
        }

        $options = $this->mergePreset($options, $mode,$imageBase64, $access_token);

        $bean = new SDBean($this->getAllOptions($options));
        $count = $bean->getBatchCount();
        if($count > self::SD_MAX_COUNT) throw new BusinessException(ErrorCode::ERROR_SD_MAX_COUNT);

        $this->generateImage($bean, $message_id, $mode, $access_token, $debug);

        $params                 = $bean->toArray();
        $params['access_token'] = $access_token;
        EngineQueue::make()
                   ->setMessageId($message_id)
                   ->setParams($params)
                   ->setEngineClass(static::class)
                   ->setProgress(0)
                   ->setDelay(1)
                   ->push();
        return [
            'message_id' => $message_id
        ];
    }

    #[TaskAsync]
    public function generateImage(SDBean $bean, $message_id, $mode, $access_token, $debug) {
        $imgMethod = in_array($mode, ['image','redraw']) ? 'img2img' : 'txt2img';
        $params = $this->toSDParams($bean, $message_id, $imgMethod, $mode);

        $debug && put_log_file($params, '$params', 'logs/sd');

        if(empty($bean->getInitImages())) {
            $res = $this->txt2img($params, $access_token);
        }else {
            if($bean->getDefinition()) {
                $this->extraImage($params['init_images'], $bean->getResize(), $access_token);
            }else {
                $res = $this->$imgMethod($params, $access_token);
            }
        }
        $debug && put_log_file($res, '$res', 'logs/sd');

        $aiEvent = new AIGenProcessEvent();
        $aiEvent->setMessageId($message_id);

        $attachments = [];
        if(!empty($res['images'])) {
            $step    = 5;
            $process = 100;
            $images  = $res['images'] ?? []; // 图片
            $count   = $bean->getBatchCount();
            $this->filterRawImage($mode, $count, $images);

            foreach($images as $key => $image) {
                $url = $this->aiOssService->saveBase64($image);
                try {
                    $this->bce->image($url);
                } catch(\Exception $e) {
                    app_event_dispatch(new AIGenProcessEvent($message_id, step: 6, error: '生成内容违规', is_show: 0));
                    throw new BusinessException(ErrorCode::ERROR_INTERNAL_REQUEST_BAN);
                }
                $path     = parse_url($url, PHP_URL_PATH);
                $filename = basename($path);

                $attachments[] = ['id'           => $message_id . '_' . ($key + 1),
                                  'url'          => $url,
                                  'size'         => $this->getSize($url),
                                  'width'        => $bean->getHiresFix() ? $bean->getWidth() * 2 : $bean->getWidth(),
                                  'height'       => $bean->getHiresFix() ? $bean->getHeight() * 2 : $bean->getHeight(),
                                  'filename'     => $filename,
                                  'proxy_url'    => $url,
                                  'content_type' => 'image/png',
                ];
            }
        } else {
            $step    = 7;
            $process = 0;
            $aiEvent->setError($res['error'] ?? '');
        }
        $this->monitoring($attachments, $bean);

        $this->redis->setex('sd_' . $message_id, 600, $step);
        $aiEvent->setStep($step);
        $aiEvent->setProcess($process);
        $aiEvent->setAttachments($attachments);
        return app_event_dispatch($aiEvent);
    }

    public function asyncProcess($message_id, $progress = 0, $params = []) {
        $access_token = $params['access_token'] ?? '';
        $aiEvent      = new AIGenProcessEvent();
        $aiEvent->setMessageId($message_id);
        if(empty($message_id)) return false;

        // 获取进度和结果
        $result = $this->getProgress($message_id, $access_token);

        /*'当前步骤 0未提交 1等待中 2关键词优化中 3已提交 4生成中 5成功 6违规 7返点'*/
        $step = 0; // 未提交
        //            $sdProgress = $result['progress'] ?? 0;
        $job_count = $result['state']['job_count'] ?? 0; // -1等待中  1生成中  0完成or不存在

        if($this->redis->exists('sd_' . $message_id)) {
            $rkey = $this->redis->get('sd_' . $message_id);
            $step = (int)$rkey;
            $aiEvent->setStep($step);
            $aiEvent->setProcess($step == 5 ? 100 : 0);
            return app_event_dispatch($aiEvent);
        }

        if($job_count == -1) {
            $aiEvent->setStep(1);
            $aiEvent->setProcess((int)$progress);
            app_event_dispatch($aiEvent);
            return EngineQueue::make()
                              ->setEngineClass(static::class)
                              ->setMessageId($message_id)
                              ->setStep($step)
                              ->setProgress($progress)
                              ->setParams($params)
                              ->setDelay(1)
                              ->push();
        }

        $progress = $this->simulateProgress($progress);

        if($progress >= 100) {
            $step     = 5;
            $progress = 100;
        } else {
            $memory = $this->memory($access_token);
            if(!empty($memory) && isset($memory['cuda']['system']['free']) && $memory['cuda']['system']['free'] < 100000000) {
                EngineAlarm::make()
                           ->setEngineName('ai.image.sd.v1')
                           ->setErrorMsg('CUDA显存不足: ' . $memory['cuda']['system']['free'] . '; message_id: ' . $message_id)
                           ->send();
                $step = 7;
                $aiEvent->setError('当前资源不足');
                $aiEvent->setStep($step);
                $aiEvent->setProcess((int)$progress);
                return app_event_dispatch($aiEvent);
            }

            $step = 4; // 生成中
            $aiEvent->setStep($step);
            $aiEvent->setProcess((int)$progress);
            app_event_dispatch($aiEvent);
            return EngineQueue::make()
                              ->setEngineClass(static::class)
                              ->setMessageId($message_id)
                              ->setStep($step)
                              ->setProgress($progress)
                              ->setParams($params)
                              ->setDelay(1)
                              ->push();
        }
        $aiEvent->setStep($step);
        $aiEvent->setProcess($progress);
        return app_event_dispatch($aiEvent);
    }

    protected function client(array $options = []): Client {
        return $this->clientFactory->create(array_merge([
            'base_uri' => env('STABLEDIFFUSION', 'http://api-eic.mit.cn'),
            'timeout'  => 0,
            'debug'    => false,
        ], $options));
    }

    private function getJson($url, $options = []) {
        return $this->requestJson('GET', $url, $options);
    }

    protected function postJson($url, array $options = []) {
        return $this->requestJson('POST', $url, $options);
    }

    //    private function putJson($url, array $options = []) {
    //        return $this->requestJson('PUT', $url, $options);
    //    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     * @return mixed
     * @throws GuzzleException
     */
    private function requestJson(string $method, string $uri = '', array $options = []): mixed {
        $response = static::client()->request($method, $uri, $options);
        $body     = (string)$response->getBody();
        return json_decode($body, true);
        //        $result   = json_decode($body, true);
        //            if($response->getStatusCode() !== 200 || json_last_error() !== JSON_ERROR_NONE) {
        //                p($body);
        //            }
        //        return $result;
        //        try {
        //        }
        //        catch(ClientException $e) {
        //            return ['status_code' => $e->getResponse()->getStatusCode(), 'client_exception' => true, 'msg' => $e];
        //        } catch(ServerException $e) {
        //            return ['status_code' => $e->getResponse()->getStatusCode(), 'server_exception' => true, 'msg' => $e->getMessage()];
        //        } catch(\Exception $e) {
        //            p($e->getMessage());
        //            return ['code' => $e->getCode(), 'msg' => $e->getMessage()];
        //        }
    }

    /**
     * option参数
     * @param array $options
     * @return array
     */
    protected function getAllOptions(array $options): array {
        $hires_fix = isset($options['quality']) && $options['quality'] == 2 ? true : ($options['hires_fix'] ?? false);
        $img_scale = $options['img_scale'] ?? '';
        // 比例转换
        $options = $this->imgScaleToPX($img_scale, $options);

        return [
            'model'                                => $options['model'],
            'prompt'                               => $options['prompt'] ?? '',
            'negative_prompt'                      => $options['negative_prompt'] ?? '',
            //            'style'                                => $options['style'] ?? [], 不需要风格了
            'style'                                => [],
            'sampling_method'                      => $options['sampling_method'] ?? 'Euler a',
            'seed'                                 => $options['seed'] ?? -1,
            'sampling_steps'                       => $options['sampling_steps'] ?? 20,
            'hires_fix'                            => $hires_fix,
            'cfg_scale'                            => $options['cfg_scale'] ?? 7,
            'batch_count'                          => $options['num'] ?? $options['batch_size'] ?? 1,
            'width'                                => $options['width'] ?? 521,
            'height'                               => $options['height'] ?? 521,
            'override_settings'                    => $options['override_settings'] ?? [],
            'upscaler'                             => $options['upscaler'] ?? 'Latent',
            'hires_upscale'                        => $options['hires_upscale'] ?? 2,
            'denoising_strength'                   => $options['denoising_strength'] ?? 0.4,
            'hires_steps'                          => $options['hires_steps'] ?? 15,
            'eta'                                  => $options['eta'] ?? 0,
            'clip_skip'                            => $options['clip_skip'] ?? 0,
            'alwayson_scripts'                     => $options['alwayson_scripts'] ?? [],
            'restore_faces'                        => $options['restore_faces'] ?? false,
            's_tmax'                               => $options['s_tmax'] ?? 0,
            's_churn'                              => $options['s_churn'] ?? 0,
            's_noise'                              => $options['s_noise'] ?? 1,
            'n_iter'                               => $options['n_iter'] ?? 1,
            'send_images'                          => $options['send_images'] ?? true,
            'save_images'                          => $options['save_images'] ?? true,
            'tiling'                               => $options['tiling'] ?? false,
            'do_not_save_samples'                  => $options['do_not_save_samples'] ?? false,
            'do_not_save_grid'                     => $options['do_not_save_grid'] ?? false,
            'override_settings_restore_afterwards' => $options['override_settings_restore_afterwards'] ?? true,
            's_min_uncond'                         => $options['s_min_uncond'] ?? 0,
            'subseed'                              => $options['subseed'] ?? -1,
            'subseed_strength'                     => $options['subseed_strength'] ?? 0,
            'seed_resize_from_h'                   => $options['seed_resize_from_h'] ?? -1,
            'seed_resize_from_w'                   => $options['seed_resize_from_w'] ?? -1,
            's_tmin'                               => $options['s_tmin'] ?? 0,
            'firstphase_width'                     => $options['firstphase_width'] ?? 0,
            'firstphase_height'                    => $options['firstphase_height'] ?? 0,
            'hr_second_pass_steps'                 => $options['hr_second_pass_steps'] ?? 0,
            'hr_resize_x'                          => $options['hr_resize_x'] ?? 0,
            'hr_resize_y'                          => $options['hr_resize_y'] ?? 0,
            'init_images'                          => $options['init_images'] ?? [], // img2img参考图
            'mask'                                 => $options['mask'] ?? '', // img2img遮罩图
            /*
               "mask": "string",
              "mask_blur": 0,
              "mask_blur_x": 4,
              "mask_blur_y": 4,
              "inpainting_fill": 0,
              "inpaint_full_res": true,
              "inpaint_full_res_padding": 0,
              "inpainting_mask_invert": 0,
              "initial_noise_multiplier": 0,
             * */
        ];
    }

    /**
     * 转换sd接口参数
     * @param SDBean $bean
     * @param string $message_id
     * @param string $imgMethod
     * @param string $mode
     * @return array
     */
    protected function toSDParams(SDBean $bean, string $message_id, string $imgMethod, string $mode): array {
        $res = [
            "task_id"                              => $message_id,
            "enable_hr"                            => $bean->getHiresFix(),
            "denoising_strength"                   => $bean->getDenoisingStrength(),
            "firstphase_width"                     => $bean->getFirstphaseWidth(),
            "firstphase_height"                    => $bean->getFirstphaseHeight(),
            "hr_scale"                             => $bean->getHiresUpscale(), // float
            "hr_upscaler"                          => $bean->getUpscaler(), // string
            "hr_second_pass_steps"                 => $bean->getHrSecondPassSteps(),
            "hr_resize_x"                          => $bean->getHrResizeX(),
            "hr_resize_y"                          => $bean->getHrResizeY(),
            "prompt"                               => $bean->getPrompt(),
            "styles"                               => $bean->getStyle(),
            "seed"                                 => $bean->getSeed(),
            "subseed"                              => $bean->getSubseed(),
            "subseed_strength"                     => $bean->getSubseedStrength(),
            "seed_resize_from_h"                   => $bean->getSeedResizeFromH(),
            "seed_resize_from_w"                   => $bean->getSeedResizeFromW(),
            "sampler_name"                         => $bean->getSamplingMethod(), // 采样方式
            "batch_size"                           => $bean->getBatchCount(),
            "n_iter"                               => $bean->getNIter(),
            "steps"                                => $bean->getSamplingSteps(),
            "cfg_scale"                            => $bean->getCfgScale(),
            "width"                                => $bean->getWidth(),
            "height"                               => $bean->getHeight(),
            'eta'                                  => $bean->getEta(),
            "restore_faces"                        => $bean->getRestoreFaces(),
            "tiling"                               => $bean->getTiling(),
            "do_not_save_samples"                  => $bean->getDoNotSaveSamples(),
            "do_not_save_grid"                     => $bean->getDoNotSaveGrid(),
            "negative_prompt"                      => $bean->getNegativePrompt(),
            "s_min_uncond"                         => $bean->getSMinUncond(),
            "s_churn"                              => $bean->getSChurn(),
            "s_tmax"                               => $bean->getSTmax(),
            "s_tmin"                               => $bean->getSTmin(),
            "s_noise"                              => $bean->getSNoise(),
            "override_settings"                    => [
                "sd_model_checkpoint"      => $bean->getModel(),
                'CLIP_stop_at_last_layers' => $bean->getClipSkip()
            ],
            "override_settings_restore_afterwards" => $bean->getOverrideSettingsRestoreAfterwards(),
            'sampler_index'                        => '',
            //            "sampler_index"                        => $bean->getSamplingMethod(), // 問題
            "script_args"                          => [
            ],
            "script_name"                          => "",
            "send_images"                          => $bean->getSendImages(),
            "save_images"                          => $bean->getSaveImages(),
            "alwayson_scripts"                     => (object)$bean->getAlwaysonScripts(),
        ];
        if($imgMethod == 'img2img') {
            $res['init_images'] = $bean->getInitImages();
//            $res['resize_mode'] = 0; // 0：仅调整大小  1：裁剪并调整大小   2：调整大小并填充

            // 重绘蒙版
            if($mode == 'redraw') {
                return [
                    'init_images' => $bean->getInitImages(),
                    'resize_mode' => 0,
                    'denoising_strength' => 0.75,
                    'image_cfg_scale' => 0,
                    'mask' => $bean->getMask(),
                    'mask_blur' => 6,
                    'mask_blur_x' => 4,
                    'mask_blur_y' => 4,
                    'inpainting_fill' => 0,
                    'inpaint_full_res' => true,
                    'inpaint_full_res_padding' => 32,
                    'inpainting_mask_invert' => 0,
                    'initial_noise_multiplier' => 1,
                    'task_id' => $message_id,
                    'prompt' => $bean->getPrompt(),
//                    'negative_prompt' => '',
                    "override_settings"                    => [
                        "sd_model_checkpoint"      => $bean->getModel(),
                        'CLIP_stop_at_last_layers' => $bean->getClipSkip()
                    ],
                    'styles' => [],
                    'seed' => -1,
                    'sampler_index' => 'Euler a',
                    'steps' => 20,
                    'cfg_scale' => 12
                ];
            }

//            $res['mask'] = $bean->getMask();
//            $res['mask_blur'] = 0; // 蒙版模糊半径
//            $res['mask_blur_x'] = 4;
//            $res['mask_blur_y'] = 4;
//            $res['inpainting_fill'] = 0; // 蒙版遮住的内容， 0填充， 1原图 2潜空间噪声 3潜空间数值零
//            $res['inpaint_full_res'] = false; // 重绘区域: true全图  false仅蒙版
//            $res['inpaint_full_res_padding'] = 0;
//            $res['inpainting_mask_invert'] = 0; // 蒙版模式: 0重绘蒙版内容 1 重绘非蒙版内容
//            $res['initial_noise_multiplier'] = 1;
//            $res['denoising_strength'] = 0.9;
        }
        return $res;
    }

    /**
     * 合并预设配置
     * @param array  $options
     * @param string $mode
     * @param string $imageBase64
     * @param string $access_token
     * @return array
     */
    protected function mergePreset(array $options, string $mode, string $imageBase64, string $access_token): array {
        if(empty($options['style_img'])) $options['style_img'][] = 'xieshi'; // 默认-写实
        $sdConfig = SDConfig::where('mode_code', $options['style_img'][0])->first();
        if(empty($sdConfig)) throw new BusinessException(ErrorCode::ERROR_SD_CREATE_UNKNOW_STYLE);

        // 获取模型列表
        $sdModels         = $this->getModels($access_token);
        $options['model'] = array_column($sdModels, null, 'model_name')[$sdConfig->model_name]['title'] ?? '';

        // 参数合并 (采样方式, 采样步数, 词条相关性, 是否高清, seed, 高清采样步数, 重绘幅度, 放大倍率, 放大算法, 跳过层数)
        $mode = $options['mode'] ?? '';

        if(!empty($sdConfig->config)) {
            $config               = $sdConfig->config;

            $prompt_type = $config['prompt_type'] ?? 0;
            $configPrompt         = $config['prompt'] ?? '';
            $configNegativePrompt = $config['negative_prompt'] ?? '';
            unset($config['prompt'], $config['negative_prompt']);

            if($mode === 'redraw') $prompt_type = 3;
            $options['prompt']          = $this->promptHandle($options['prompt'], $configPrompt, $prompt_type);
            $negative_prompt            = $this->negativePrompt($configNegativePrompt);
            $options['negative_prompt'] = $negative_prompt;
            $options                    = array_merge($options, $config);
        }


        if(in_array($mode, ['qrcode', 'image', 'outline']) && empty($imageBase64)) {
            throw new BusinessException(ErrorCode::ERROR_SD_UNKNOWN);
        }

        if($imageBase64) {
            $options['init_images']        = [$imageBase64];
        }
        if($mode == 'image') { # 图生图
            $options['denoising_strength'] = 0.6;
            $options['sampler_index']      = 'Euler';
        } else {
            $options = $this->setScript($options, $imageBase64, $mode);
        }

        return $options;
    }

    private function promptHandle($prompt, $configPrompt, $prompt_type): string {
        $prompt = trim($prompt, ",");
        $configPrompt = trim($configPrompt, ",");
        $default = trim(self::DEFAULT_PROMPT, ",");

        $res = match ($prompt_type) {
            0 => [$prompt, $default], // 用户和默认值拼接 (目前默认)
            1 => [$prompt, $configPrompt], // 不要默认值
            2 => [$prompt, $configPrompt, $default],// 3种拼接
            3 => [$prompt], // 只要用户输入     redraw
            4 => [str_replace('{PROMPT}', $prompt, $configPrompt), $default],
            5 => [str_replace('{PROMPT}', $prompt, $configPrompt)],
            6 => [$configPrompt, $default],
            7 => [$configPrompt]
        };
        return implode(",", $res);
    }

    private function negativePrompt($negative_prompt): string {
        $default = self::DEFAULT_NEGATIVEPROMPT;
        if(empty($negative_prompt)) return $default; // 默认的

        $negative_prompt2 = explode(",", $negative_prompt);
        $default2         = explode(",", $default);
        return implode(",", array_unique(array_merge($negative_prompt2, $default2)));
    }

    private function getSize($url): int|string {
        try {
            $response = $this->client->head($url);
            return $response->getHeaderLine('Content-Length') ?: 0;
        } catch(Throwable) {
            return 0;
        }
    }

    protected function getApiAccount() {
        $msg_id = $this->aiBean->getMsgId();
        if($msg_id) {
            $key_id = Message::where('id', $msg_id)->value('key_id');
            if(empty($key_id)) {
                $key_id  = $this->aiBean->getKeyId();
                $aiEvent = new AIGenProcessEvent();
                $aiEvent->setMessageId($this->aiBean->getMessageId());
                $aiEvent->setKeyId($key_id);
                app_event_dispatch($aiEvent);
            } else {
                $api_key = ApiAccount::where('key_id', $key_id)->value('api_key');
                $this->aiBean->setKeyId($key_id);
                $this->aiBean->setApiKey($api_key);
            }
        }
        return $this->aiBean->getApiKey();
    }

    public function handleAttachments(Message $message) {
        if(empty($message->attachments)) {
            return $message;
        }
        if($message->num < 1 || $message->num > 4) {
            $message->num = 1;
        }
        $attachments = [];
        $step        = $message->step;
        foreach($message->attachments as $i => $attachment) {
            $attachment['original_url']  = $attachment['url'];
            $attachment['images']        = $message->images;
            $attachment['step']          = $message->step;
            $attachment['progress']      = $message->progress;
            $attachment['proxy_url']     = $attachment['url'];
            $attachment['thumb_url']     = $attachment['url'];
            $attachment['attachment_id'] = $message->message_id . '-0';
            $attachments[]               = $attachment;
        }
        $message->step        = $step;
        $message->attachments = $attachments;
        return $message;
    }

    /**
     * @param $attachments
     * @param $bean
     * @return void
     * @throws GuzzleException
     */
    private function monitoring($attachments, $bean): void {
        $secret    = 'SEC6a1ff4daaa04807ca136ca4af2c9d11257a4f973edc636cd15db0f297ef002b6';
        $timestamp = time() * 1000;
        $sign      = hash_hmac('sha256', $timestamp . "\n" . $secret, $secret, true);
        $sign      = urlencode(base64_encode($sign));

        $thumb       = $attachments[0]['url'] ?? '';
        $attachments = json_encode($attachments, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $bean        = json_encode($bean->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $this->client->post('https://oapi.dingtalk.com/robot/send', [
            'query' => [
                'access_token' => 'ff0f2488e5a57d967d44e180bc7dde5e5732cd0bfd812ef3aba752f1e8709334',
                'timestamp'    => $timestamp,
                'sign'         => $sign,
            ],
            'json'  => [
                'msgtype'  => 'markdown',
                'markdown' => [
                    'title' => 'SD生成任务',
                    'text'  => "![thumb]({$thumb})\n ### 结果: \n```\n{$attachments}\n``` \n ### 完整参数: \n```\n{$bean}\n```",
                ],
            ],
        ]);
    }

    private function txt2img(array $params, $access_token) {
        return $this->postJson('/sdapi/v1/txt2img', [
            'headers' => [
                'authorization' => "Bearer {$access_token}",
            ],
            'json'    => $params
        ]);
    }

    private function getProgress(string $task_id, $access_token) {
        return $this->getJson('/sdapi/v1/progress', [
            'headers' => [
                'authorization' => "Bearer {$access_token}",
            ],
            'query'   => [
                'task_id'            => $task_id,
                'skip_current_image' => false
            ]
        ]);
    }

    private function memory($access_token) {
        return $this->getJson('/sdapi/v1/memory', [
            'headers' => [
                'authorization' => "Bearer {$access_token}",
            ],
        ]);
    }

    private function getModels($access_token) {
        return $this->getJson('/sdapi/v1/sd-models', [
            'headers' => [
                'authorization' => "Bearer {$access_token}",
            ],
        ]);
    }

    private function extraImage($image, $resize, $access_token) {
        return $this->getJson('/sdapi/v1/extra-single-image', [
            'headers' => [
                'authorization' => "Bearer {$access_token}",
            ],
            'json' => [
                "resize_mode" => 0,
                "show_extras_results" => true,
                "gfpgan_visibility" => 0,
                "codeformer_visibility" => 0,
                "codeformer_weight" => 0,
                "upscaling_resize" => $resize,
                "upscaling_resize_w" => 512,
                "upscaling_resize_h" => 512,
                "upscaling_crop" => true,
                "upscaler_1" => "RealESRGAN_x4plus",
                "upscaler_2" => "None",
                "extras_upscaler_2_visibility" => 0,
                "upscale_first" => true,
                "image" => $image
            ]
        ]);
    }

    private function img2img(array $params, $access_token) {
        return $this->postJson('/sdapi/v1/img2img', [
            'headers' => [
                'authorization' => "Bearer {$access_token}",
            ],
            'json'    => $params
        ]);
    }
    //    public function getStyles($access_token) {
    //        return $this->getJson('/sdapi/v1/prompt-styles', [
    //            'headers' => [
    //                'authorization' => "Bearer {$access_token}",
    //            ],
    //        ]);
    //    }
    /**
     * @param string|null $prompt
     * @return mixed|string|null
     */
    private function baiduTranslate(?string $prompt): mixed {
        if(preg_match('/\p{Han}/u', $prompt)) {
            $prompt = $this->baiduTranslate->translate($prompt) ?: $prompt;
        }
        return $prompt;
    }

    /**
     * 在线图片转base64
     * @param string $imageUrl
     * @return string
     */
    private function encodeImage(string $imageUrl): string {
        if(empty($imageUrl)) return '';

        $image = @file_get_contents($imageUrl); // 使用@符号来忽略错误，并将返回值赋给$image变量
        if(!$image !== false) {
            throw new BusinessException(ErrorCode::ERROR_SD_QRCODE_ERROR);
        }
        return base64_encode($image); // 将内容转换为Base64编码
    }

    /**
     * 比例转换分辨率
     * @param string $img_scale
     * @param array  $options
     * @return array
     */
    private function imgScaleToPX(string $img_scale, array $options): array {
        $scaleOptions = [
            '1:1'  => ['width' => 512, 'height' => 512],
            '4:3'  => ['width' => 591, 'height' => 443],
            '3:4'  => ['width' => 443, 'height' => 591],
            '2:1'  => ['width' => 724, 'height' => 362],
            '1:2'  => ['width' => 362, 'height' => 724],
            '16:9' => ['width' => 682, 'height' => 383],
            '9:16' => ['width' => 383, 'height' => 682]
        ];
        if(isset($scaleOptions[$img_scale])) {
            $options['width']  = $scaleOptions[$img_scale]['width'];
            $options['height'] = $scaleOptions[$img_scale]['height'];
        }

        return $options;
    }

    /**
     * 设置控制网配置
     * @param array  $options
     * @param string $imageBase64
     * @param string $mode
     * @return array
     */
    private function setScript(array $options, string $imageBase64, string $mode): array {
        $args = [
            'qrcode'  => [
                'enabled'        => true,
                'module'         => 'none',
                'model'          => 'control_v1p_sd15_qrcode_monster_v2 [5e5778cb]',
                'weight'         => $options['effect'] ?? 1.6,
                'image'          => $imageBase64,
                'resize_mode'    => 1,
                'lowvram'        => false,
                'guidance_start' => 0,
                'guidance_end'   => 1.0,
                'control_mode'   => 0,
                'pixel_perfect'  => true
            ],
            'outline' => [
                'enabled'        => true,
                'module'         => 'lineart',
                'model'          => 'control_v11p_sd15_lineart [43d4be0d]',
                'weight'         => $options['effect'] ?? 1,
                'image'          => $imageBase64,
                'resize_mode'    => 1,
                'lowvram'        => false,
                'guidance_start' => 0,
                'guidance_end'   => 1.0,
                'control_mode'   => 0,
                'pixel_perfect'  => true
            ],
            'scrawl'  => [
                'enabled'        => true,
                'module'         => 'invert (from white bg & black line)',
                'model'          => 'control_v11p_sd15_scribble [d4ba51ff]',
                'weight'         => $options['effect'] ?? 1,
                'image'          => $imageBase64,
                'resize_mode'    => 1,
                'lowvram'        => false,
                'guidance_start' => 0,
                'guidance_end'   => 1.0,
                'control_mode'   => 0,
                'pixel_perfect'  => true
            ],
//            'redraw'  => [
//                'enabled'        => true,
//                'module'         => 'inpaint_only',
//                'model'          => 'control_v11p_sd15_inpaint [ebff9138]',
//                'weight'         => $options['effect'] ?? 1,
//                'image'          => $imageBase64,
//                'resize_mode'    => 1,
//                'lowvram'        => false,
//                'guidance_start' => 0,
//                'guidance_end'   => 1.0,
//                'control_mode'   => 0,
//                'pixel_perfect'  => true
//            ]
        ];

        if(isset($args[$mode])) {
            $options['alwayson_scripts'] = [
                'controlnet' => [
                    'args' => [
                        $args[$mode]
                    ]
                ]
            ];
        }
        return $options;
    }

    /**
     * 过略参考图
     * @param       $mode
     * @param       $count
     * @param array $images
     * @return void
     */
    private function filterRawImage($mode, $count, array &$images): void {
        if(in_array($mode, ['outline', 'qrcode', 'scrawl'])) {
            if($count > 1) {
                array_shift($images);
            }
            array_pop($images);
        }
    }

    /**
     * 模拟进度
     * @param $currentProgress
     * @return int
     */
    private function simulateProgress($currentProgress): int {
        if($currentProgress < 70) {
            $currentProgress += mt_rand(1, 4);
        } else {
            $currentProgress += 1;
        }
//        if($currentProgress <= 50) {
//            $currentProgress += mt_rand(3, 6);
//        } else if ($currentProgress < 90) {
//            $currentProgress += mt_rand(1, 4);
//        } else {
//            $currentProgress += 1;
//        }
//
//        if ($currentProgress >= 100) $currentProgress = 99;
        return $currentProgress;
    }
}
