## Start project locally

### 1. Disable GIT file mode

`git config core.fileMode false`

### 2. Install Docker and docker-compose

https://docs.docker.com/compose/install/

### 3. Create Docker .env file

`cp devops/local/.env.example devops/local/.env`

### 4. Build and Project:

`cd devops/local/`
`docker-compose up`

or

`sudo docker-compose up`

### 5. Connect to Docker VM:

`sudo docker exec -it cargo-guru-front bash`

### 6. Site will be available by this URL:

http://localhost:8067
