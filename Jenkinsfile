pipeline {
    agent any

    environment {
        DOCKERHUB_USER = 'your_dockerhub_username'
        DOCKERHUB_PASS = credentials('dockerhub-credentials')
    }

    stages {
        stage('Checkout Code') {
            steps {
                git branch: 'main', url: 'https://github.com/aditya/sahaay-microservices.git'
            }
        }

        stage('Build Docker Images') {
            steps {
                script {
                    sh 'docker build -t report-service ./report-service'
                    sh 'docker build -t view-service ./view-service'
                    sh 'docker build -t auth-service ./auth-service'
                    sh 'docker build -t sahaay-frontend ./frontend'
                }
            }
        }

        stage('Login to DockerHub') {
            steps {
                sh 'echo $DOCKERHUB_PASS | docker login -u $DOCKERHUB_USER --password-stdin'
            }
        }

        stage('Push Images to DockerHub') {
            steps {
                script {
                    sh '''
                    docker tag report-service $DOCKERHUB_USER/report-service:latest
                    docker tag view-service $DOCKERHUB_USER/view-service:latest
                    docker tag auth-service $DOCKERHUB_USER/auth-service:latest
                    docker tag sahaay-frontend $DOCKERHUB_USER/sahaay-frontend:latest

                    docker push $DOCKERHUB_USER/report-service:latest
                    docker push $DOCKERHUB_USER/view-service:latest
                    docker push $DOCKERHUB_USER/auth-service:latest
                    docker push $DOCKERHUB_USER/sahaay-frontend:latest
                    '''
                }
            }
        }

        stage('Deploy to EC2') {
            steps {
                sh '''
                docker pull $DOCKERHUB_USER/report-service:latest
                docker pull $DOCKERHUB_USER/view-service:latest
                docker pull $DOCKERHUB_USER/auth-service:latest
                docker pull $DOCKERHUB_USER/sahaay-frontend:latest

                docker stop report-service view-service auth-service sahaay-frontend || true
                docker rm report-service view-service auth-service sahaay-frontend || true

                docker run -d -p 8002:80 --name report-service $DOCKERHUB_USER/report-service:latest
                docker run -d -p 8003:80 --name view-service $DOCKERHUB_USER/view-service:latest
                docker run -d -p 8001:80 --name auth-service $DOCKERHUB_USER/auth-service:latest
                docker run -d -p 80:80 --name sahaay-frontend $DOCKERHUB_USER/sahaay-frontend:latest
                '''
            }
        }
    }
}
