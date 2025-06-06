# places-management

API de Gerenciamento de Lugares

## Tecnologias Utilizadas

- [PHP 8](https://www.php.net/)
- [Laravel 12](https://laravel.com/docs/12.x)
- [Docker e Docker Compose](https://www.docker.com/)
- [PostgreSQL](https://www.postgresql.org/)

## Features Desenvolvidas

- Login
- Validação de token
- Cadastro de Usuário
- Busca de um Usuário
- Atualização de Usuário
- Deleção de Usuário
- Listagem de Lugares com filtros
- Cadastro de Lugar
- Busca de um Lugar
- Atualização de Lugar
- Deleção de Lugar

### Configuração

1. Fazer a cópia do projeto para sua máquina

   ```bash
    git clone git@github.com:mauricioccardoso/places-management.git

    ou

    git clone https://github.com/mauricioccardoso/places-management.git
   ```

2. Caso tenha o Docker e Docker compose configurado na sua máquina, siga para [Docker e Docker Compose](#configuração-com-docker-e-docker-compose).
   Caso não tenha docker, continue para a coniguração do backend abaixo.

   ### BackEnd

3. Acessa a pasta do projeto a partir do terminal e acessa a pasta backend

   ```bash
    cd backend
   ```

4. Fazer a instalação das dependências do laravel

   ```bash
    composer install
   ```

5. Copiar o arquivo .env.example, renomear para .env e configurar as variáveis de acordo com seu banco de dados. Ex:

   ```bash
    DB_CONNECTION=mysql
    DB_HOST=places-db
    DB_PORT=5432
    DB_DATABASE=places-db
    DB_USERNAME=root
    DB_PASSWORD=rootpass
   ```

6. Caso o laravel não gere um chave, usar o comando para gerar uma nova chave de criptografia

   ```bash
    php artisan key:generate
   ```

7. Executar o comando para criar as tabelas no banco de dados e as seeds

   ```bash
    php artisan migrate --seed
   ```

   Obs.: Para subir o servido backend localmente utilize o comando dentro da pasta backend

   ```bash
    php artisan serve --host=0.0.0.0 --port=8080

    ou

    php artisan serve --host=localhost --port=8080
   ```

8. Acessar a documentação no navegador:

Backend - Server
[http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)

---

### Configuração com Docker e Docker compose

1. Acessa a pasta raiz do projeto a partir do terminal ou com o editor de texto.

2. Na pasta "Backend", copiar o arquivo ".env.exemple" e renomear para ".env".
   E configurar as variáveis de ambiente.

   ```bash
    ## Docker Database Configuration
    DB_CONNECTION=pgsql
    DB_HOST=places-database
    DB_PORT=5432
    DB_DATABASE=places-db
    DB_USERNAME=root
    DB_PASSWORD=rootpass
   ```

3. Voltar para a raiz do projeto e usar o comando para subir os containers

   ```bash
    docker compose up -d
   ```

4. Após os containers estiverem prontos, acessar a documentação no navegador:

Backend - Server
[http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)
