pipeline {
    agent any

    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-creds')
        GITHUB_CREDENTIALS = credentials('newtoken')
        AWS_DEFAULT_REGION = 'ap-south-1'

        DB_HOST = 'database-1.cv0ug8sq4qhc.ap-south-1.rds.amazonaws.com'
        DB_USER = 'admin'
        DB_PASS = 'Aditya1234'
        DB_NAME = 'city_records'
    }

    stages {
        stage('Checkout Code') {
            steps {
                git branch: 'main', 
                    credentialsId: 'newtoken',
                    url: 'https://github.com/AdiiiAdiAdi/Sahaay-microservices.git'
            }
        }

        stage('Build Docker Images') {
            steps {
                script {
                    echo 'Building all microservice Docker images...'
                    sh '''
                    cd report-service && docker build -t adiiiadiadi/report-service:latest . && cd ..
                    cd view-service && docker build -t adiiiadiadi/view-service:latest . && cd ..
                    cd auth-service && docker build -t adiiiadiadi/auth-service:latest . && cd ..
                    cd frontend && docker build -t adiiiadiadi/sahaay-frontend:latest . && cd ..
                    '''
                }
            }
        }

        stage('Push to DockerHub') {
            steps {
                script {
                    echo 'Logging into DockerHub and pushing images...'
                    sh '''
                    echo $DOCKERHUB_CREDENTIALS_PSW | docker login -u $DOCKERHUB_CREDENTIALS_USR --password-stdin
                    
                    docker push adiiiadiadi/report-service:latest
                    docker push adiiiadiadi/view-service:latest
                    docker push adiiiadiadi/auth-service:latest
                    docker push adiiiadiadi/sahaay-frontend:latest
                    '''
                }
            }
        }

        stage('Deploy Containers') {
            steps {
                script {
                    echo 'Deploying containers on EC2...'
                    sh '''
                    docker stop report-service || true && docker rm report-service || true
                    docker stop view-service || true && docker rm view-service || true
                    docker stop auth-service || true && docker rm auth-service || true
                    docker stop sahaay-frontend || true && docker rm sahaay-frontend || true

                    docker run -d -p 8002:80 \
                        -e DB_HOST=$DB_HOST -e DB_USER=$DB_USER -e DB_PASS=$DB_PASS -e DB_NAME=$DB_NAME \
                        --name report-service adiiiadiadi/report-service:latest

                    docker run -d -p 8003:80 \
                        -e DB_HOST=$DB_HOST -e DB_USER=$DB_USER -e DB_PASS=$DB_PASS -e DB_NAME=$DB_NAME \
                        --name view-service adiiiadiadi/view-service:latest

                    docker run -d -p 8001:80 \
                        -e DB_HOST=$DB_HOST -e DB_USER=$DB_USER -e DB_PASS=$DB_PASS -e DB_NAME=$DB_NAME \
                        --name auth-service adiiiadiadi/auth-service:latest

                    docker run -d -p 80:80 \
                        --name sahaay-frontend adiiiadiadi/sahaay-frontend:latest
                    '''
                }
            }
        }
    }
}


