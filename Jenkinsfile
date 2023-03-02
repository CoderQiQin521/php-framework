pipeline {
    agent any
    options {
        buildDiscarder(logRotator(numToKeepStr: '2'))
    }
    stages {
         stage('代码质量检测') {
            steps {
                echo "跳过"
            }
         }
        stage('部署开发环境') {
            when {
                branch 'dev'
            }
            steps {
                sshPublisher(publishers: [sshPublisherDesc(configName: 'dev-123.57.143.30', transfers: [sshTransfer(cleanRemote: false, excludes: '', execCommand: '''cd /www/wwwroot/test.finance.dianmokeji.com/finance-service
                composer install''', execTimeout: 120000, flatten: false, makeEmptyDirs: false, noDefaultExcludes: false, patternSeparator: '[, ]+', remoteDirectory: 'test.finance.dianmokeji.com/finance-service', remoteDirectorySDF: false, removePrefix: '', sourceFiles: '**/**')], usePromotionTimestamp: false, useWorkspaceInPromotion: false, verbose: false)])
            }
        }
        stage('部署测试环境') {
            when {
                branch 'test'
            }
            steps {
                echo '部署测试环境'
            }
        }
        stage('部署生产环境') {
            when {
                branch 'pro'
            }
            steps {
                sshPublisher(publishers: [sshPublisherDesc(configName: 'pro-47.94.85.26', transfers: [sshTransfer(cleanRemote: false, excludes: '', execCommand: '''cd /www/wwwroot/finance.dianmokeji.com/finance-service
                composer install''', execTimeout: 120000, flatten: false, makeEmptyDirs: false, noDefaultExcludes: false, patternSeparator: '[, ]+', remoteDirectory: 'finance.dianmokeji.com/finance-service', remoteDirectorySDF: false, removePrefix: '', sourceFiles: '**/**')], usePromotionTimestamp: false, useWorkspaceInPromotion: false, verbose: false)])
            }
        }
    }
    post {
        always {
            wrap([$class: 'BuildUser']) {
                dingtalk (
                    robot: '1',
                    type:'ACTION_CARD',
                    atAll: false,
                    title: "${env.JOB_NAME}",
                    text: [
                        "### [${env.JOB_NAME}](${env.JOB_URL})",
                        '---',
                        "- 构建ID: [${BUILD_ID}](${env.BUILD_URL})",
                        "- 构建分支: ${BRANCH_NAME}",
                        "- 构建状态：<font color=${currentBuild.currentResult=='SUCCESS'?'#00EE76':'#EE0000'} >${currentBuild.currentResult}</font>",
                        "- 持续时间：${currentBuild.durationString}".split("and counting")[0],
                        "- 执行人：${env.BUILD_USER}",
                    ]
                )
            }
        }
    }
}