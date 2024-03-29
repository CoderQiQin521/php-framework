<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* index.html */
class __TwigTemplate_12b0e39bb4ca05b979ac1c79e6159a9c223bb294586bc0a1c155a4e043a1e3b8 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"zh-CN\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no\">
    <title>";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "title", [], "any", false, false, false, 6), "html", null, true);
        echo "--CoderQiQin</title>
    <link href=\"https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css\" rel=\"stylesheet\">
    <style>
        a {
            color: unset;
            text-decoration: none;
        }

        .flex {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .cn {
            color: #555;
        }

        .en {
            margin-top: 20px;
            margin-bottom: 30px;
            color: #888888;
        }

        .concat {
            margin-top: 50px;
        }

        .link {
            color: #f08d49;
        }

        .github-corner:hover .octo-arm {
            animation: octocat-wave 560ms ease-in-out
        }

        @keyframes octocat-wave {
            0%, 100% {
                transform: rotate(0)
            }
            20%, 60% {
                transform: rotate(-25deg)
            }
            40%, 80% {
                transform: rotate(10deg)
            }
        }

        @media (max-width: 500px) {
            .github-corner:hover .octo-arm {
                animation: none
            }

            .github-corner .octo-arm {
                animation: octocat-wave 560ms ease-in-out
            }
        }

        .title {
            margin-top: 180px;
        }

        @media (max-width: 960px) {
            .title {
                margin-top: 40px;
            }
        }
    </style>
</head>
<body>
<div id=\"app\">
    <a href=\"https://github.com/CoderQiQin521/php-framework\" class=\"github-corner\" aria-label=\"View source on GitHub\">
        <svg width=\"80\" height=\"80\" viewBox=\"0 0 250 250\"
             style=\"fill:#42b983; color:#fff; position: absolute; top: 0; border: 0; right: 0;\" aria-hidden=\"true\">
            <path d=\"M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z\"></path>
            <path d=\"M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2\"
                  fill=\"currentColor\" style=\"transform-origin: 130px 106px;\" class=\"octo-arm\"></path>
            <path d=\"M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z\"
                  fill=\"currentColor\" class=\"octo-body\"></path>
        </svg>
    </a>
    <div class=\"container flex\">
        <h1 class=\"title\">";
        // line 89
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "title", [], "any", false, false, false, 89), "html", null, true);
        echo "</h1>
        <div class=\"cn\">";
        // line 90
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "cn", [], "any", false, false, false, 90), "html", null, true);
        echo "</div>
        <div class=\"en\">
            ";
        // line 92
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["data"] ?? null), "en", [], "any", false, false, false, 92), "html", null, true);
        echo "
        </div>
        <div>
            <div class=\"col-lg-4 col-md-4 col-xs-12\">
                <h3>数据库支持</h3>
                <p>使用PHP_POD作为底层支持，可连接MySQL、MSSQL、Oracle、SQLite、PostgreSQL、Sybase，并且支持配置多数据连<a class=\"btn btn-link\"
                                                                                               href=\"/index/api\">演示</a>
                </p>
            </div>
            <div class=\"col-lg-4 col-md-4 col-xs-12\">
                <h3>控制器</h3>
                <p>采用了OOP进行访问调用成员函数，轻松创建Action进行访问处理事务，并且基于namespace下运行</p>
            </div>
            <div class=\"col-lg-4 col-md-4 col-xs-12\">
                <h3>模板解析</h3>
                <p>框架内置了HTML模板解析引擎，可满足程序开发的运行过程</p>
            </div>
        </div>

        <div class=\"concat\">
            <a class=\"link\" href=\"https://github.com/CoderQiQin521/php-framework\">欢迎您的star</a>
            与
            <a href=\"mailto: coderqiqin@aliyun.com\">技术交流</a>
            <div><small>此网站基于php-framework搭建</small></div>
        </div>
    </div>
</div>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  139 => 92,  134 => 90,  130 => 89,  44 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "index.html", "D:\\phpstudy_pro\\WWW\\mytest\\views\\index.html");
    }
}
