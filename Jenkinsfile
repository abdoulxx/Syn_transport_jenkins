pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                // Fix Git configuration
                bat '''
                    git config --global http.version HTTP/1.1
                    git config --global http.postBuffer 524288000
                '''
                git branch: 'main', url: 'https://github.com/abdoulxx/Syn_transport_jenkins.git'
            }
        }

        stage('Install Dependencies') {
            steps {
                bat 'composer install'
            }
        }

        stage('Static Code Analysis') {
            steps {
                script {
                    def exitCode = bat(returnStatus: true, script: '''
                        vendor\\bin\\phpstan analyse --level=max src/ --no-progress --error-format=table --memory-limit=2G
                    ''')
                    if (exitCode != 0) {
                        echo "PHPStan a détecté des erreurs, mais on continue l'exécution du pipeline."
                    }
                }
            }
        }

        stage('SQL Injection Test (Automatisation)') {
            steps {
                bat '"C:\\Users\\aboul\\AppData\\Local\\Programs\\Python\\Python313\\python.exe" tests\\automatisation_sqlmap.py'
            }
        }

        stage('Build') {
            steps {
                echo "Build stage: Compilation / Packaging (si nécessaire)"
            }
        }
        
        stage('Run Selenium Tests - Test 1') {
            steps {
                bat '"C:\\Users\\aboul\\AppData\\Local\\Programs\\Python\\Python313\\python.exe" tests\\selenium_tests.py'
            }
        }

        stage('Run Selenium Tests - Test 2') {
            steps {
                bat '"C:\\Users\\aboul\\AppData\\Local\\Programs\\Python\\Python313\\python.exe" tests\\selenium_test_pack_or.py'
            }
        }

        stage('Run spider') {
            steps {
                bat '"C:\\Users\\aboul\\AppData\\Local\\Programs\\Python\\Python313\\python.exe" tests\\spider.py'
            }
        }

        stage('Run Scan Active') {
            steps {
                bat '"C:\\Users\\aboul\\AppData\\Local\\Programs\\Python\\Python313\\python.exe" tests\\active_scan.py'
            }
        }

        stage('Run Form Authentication') {
            steps {
                bat '"C:\\Users\\aboul\\AppData\\Local\\Programs\\Python\\Python313\\python.exe" tests\\form_autentification.py'
            }
        }
    }

    post {
        always {
            echo "Pipeline terminé"
        }
    }
}
