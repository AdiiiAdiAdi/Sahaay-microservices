pipeline {
    agent any
    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-creds')
        AWS_DEFAULT_REGION = 'ap-south-1'
    }
    stages {
        stage('Clone Repository') {
            steps {
                git branch: 'main', url: 'https://github.com/AdiiiAdiAdi/Sahaay-microservices.git'
            }
        }
        stage('Build Docker Images') {
            steps {
                sh '''
                cd report-service && docker build -t report-service .
                cd ../view-service && docker build -t view-service .
                cd ../auth-service && docker build -t auth-service .
                cd ../frontend && docker build -t sahaay-frontend .
                '''
            }
        }
        stage('Run Containers (Deploy)') {
            steps {
                sh '''
                docker stop report-service || true && docker rm report-service || true
                docker stop view-service || true && docker rm view-service || true
                docker stop auth-service || true && docker rm auth-service || true
                docker stop sahaay-frontend || true && docker rm sahaay-frontend || true

                docker run -d -p 8002:80 --name report-service report-service
                docker run -d -p 8003:80 --name view-service view-service
                docker run -d -p 8001:80 --name auth-service auth-service
                docker run -d -p 80:80   --name sahaay-frontend sahaay-frontend
                '''
            }
        }
    }
}

