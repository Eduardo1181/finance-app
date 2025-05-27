# Finance App

Aplicação Laravel para controle financeiro, configurada com Docker.

## Tecnologias

- Laravel
- PHP-FPM
- Nginx
- MySQL 5.7
- phpMyAdmin
- Docker e Docker Compose

##screan shots do sistema
![Captura de tela 2025-05-27 203941](https://github.com/user-attachments/assets/d2d8e8e2-91bd-4112-9098-a9983d92b1d6)
![Captura de tela 2025-05-27 203929](https://github.com/user-attachments/assets/8ab41bfe-ea14-4c65-b872-cf98f6e3f917)
![Captura de tela 2025-05-27 204119](https://github.com/user-attachments/assets/0cdfb61e-4280-4b23-9e2e-2e9f8b1fa478)
![Captura de tela 2025-05-27 204106](https://github.com/user-attachments/assets/7a1a0581-aca9-407a-ae5b-733fa884658e)
![Captura de tela 2025-05-27 204051](https://github.com/user-attachments/assets/aacdabfc-9bfd-44f5-aef9-c9fdb3ca4260)
![Captura de tela 2025-05-27 204041](https://github.com/user-attachments/assets/da443ec2-20d6-475c-9bc3-61938049940a)
![Captura de tela 2025-05-27 204029](https://github.com/user-attachments/assets/b5ad4310-6a7e-4d1c-83cb-0e1956836ac9)
![Captura de tela 2025-05-27 204018](https://github.com/user-attachments/assets/31fdd4c6-3d61-48cb-9eb1-9f8833da023e)
![Captura de tela 2025-05-27 204007](https://github.com/user-attachments/assets/ce40bc2d-96f8-4055-bab9-d38be06bdda4)
![Captura de tela 2025-05-27 203951](https://github.com/user-attachments/assets/66b8bb34-356a-4092-8913-a06f09f74f1a)
## Requisitos
- Docker
- Docker Compose

## Instalação

```bash
git clone https://github.com/Eduardo1181/finance-app.git
cd finance-app
cp finance-app/.env.example finance-app/.env
docker-compose up -d
docker-compose exec php composer install
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate
Acesso
Aplicação: http://localhost:8083

phpMyAdmin: http://localhost:8084

Usuário: root

Senha: root

Host: mysql_db

Estrutura
finance-app/: código-fonte Laravel

nginx/: configurações do servidor web

php/: imagem Docker personalizada do PHP

docker-compose.yml: orquestração dos containers
