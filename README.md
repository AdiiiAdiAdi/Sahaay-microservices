🚀 Sahaay – Microservices & DevOps Cloud Deployment

Sahaay is a cloud-enabled civic issue reporting platform redesigned from a monolithic architecture to a microservices-based system.
The project demonstrates DevOps automation, containerization, CI/CD pipelines, cloud storage, and monitoring using modern tools.

📌 Project Overview

The original Sahaay application was a monolithic system where all functionalities were tightly coupled. This project transforms it into a modular microservices architecture enabling independent deployment, improved scalability, and better system reliability.

The system allows users to report civic issues, upload images, and view reported issues, while the backend services are deployed using Docker containers and automated using Jenkins pipelines.

🏗 Architecture

The application consists of the following microservices:

Auth Service – Handles user authentication and registration

Report Service – Handles issue submission and image uploads

View Service – Fetches and displays reported issues

Frontend Service – Nginx-based UI for user interaction

CI/CD and monitoring infrastructure include:

Jenkins (CI/CD automation)

Docker (containerization)

AWS EC2 (cloud deployment)

AWS S3 (image storage)

AWS Lambda (event-driven image handling)

Prometheus (metrics collection)

Grafana (monitoring dashboards)

⚙ Tech Stack
Backend

PHP

Apache

Frontend

HTML

CSS

JavaScript

Nginx

DevOps Tools

Docker

Jenkins

GitHub

Cloud Services

AWS EC2

AWS S3

AWS Lambda

Monitoring

Prometheus

Grafana

🔄 CI/CD Pipeline

The application uses a Jenkins pipeline to automate builds and deployments.

Pipeline Workflow
Code Commit → Jenkins CI → Docker Build → Push to DockerHub
→ Deploy Containers → Prometheus Monitoring → Grafana Dashboard
Automated Steps

Pull code from GitHub

Build Docker images for each microservice

Push images to DockerHub

Stop old containers

Deploy new containers automatically

📂 Project Structure
sahaay-microservices
│
├── auth-service
│   ├── Dockerfile
│   └── app
│       ├── login.php
│       ├── register.php
│       └── db.php
│
├── report-service
│   ├── Dockerfile
│   └── app
│       ├── report.php
│       └── db.php
│
├── view-service
│   ├── Dockerfile
│   └── app
│       ├── view.php
│       └── db.php
│
├── frontend
│   ├── Dockerfile
│   ├── index.html
│   ├── js
│   │   └── app.js
│   └── css
│
├── Jenkinsfile
└── README.md
🐳 Running with Docker
Build Images
docker build -t auth-service ./auth-service
docker build -t report-service ./report-service
docker build -t view-service ./view-service
docker build -t sahaay-frontend ./frontend
Run Containers
docker run -d -p 8001:80 --name auth-service auth-service
docker run -d -p 8002:80 --name report-service report-service
docker run -d -p 8003:80 --name view-service view-service
docker run -d -p 80:80 --name sahaay-frontend sahaay-frontend
☁ Cloud Deployment

The system is deployed on AWS EC2, where Jenkins automatically builds and deploys containers.

Images uploaded by users are stored in AWS S3, while AWS Lambda is used for automated image processing tasks.

📊 Monitoring

The system includes a monitoring stack using:

Prometheus

Collects system metrics such as:

CPU usage

Memory usage

Service health

container statistics

Grafana

Visualizes metrics through dashboards including:

resource utilization

system uptime

service performance

📈 Key Improvements

Reduced deployment time from ~30 minutes to ~3 minutes

Enabled independent microservice deployments

Improved scalability and fault isolation

Added real-time monitoring dashboards

Integrated cloud-native image storage

📸 Example Screens

Suggested screenshots for documentation:

Jenkins Pipeline Success

Docker containers running

Grafana monitoring dashboard

Prometheus metrics

AWS S3 bucket

Lambda function

🎯 Future Improvements

Kubernetes orchestration

API gateway integration

Load balancing

Auto-scaling groups

Security hardening (OAuth / JWT)

👨‍💻 Author

Aditya
DevOps & Cloud Engineering Enthusiast

GitHub:
https://github.com/AdiiiAdiAdi
