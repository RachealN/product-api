PRODUCT API

This project is a RESTful API built using Symfony, designed to manage a list of products. The application provides various endpoints to create, read, update, and delete products and includes JWT authentication for securing the endpoints. The project also includes Docker configuration for easy setup and static code analysis to ensure code quality.

Setup Instructions
Prerequisites
- PHP 7.4 and above
- Docker
- MySQL
- GIT

STEP-BY-STEP INSTALLATION GUIDELINES

1. Git Clone the Repository
    `git clone git@github.com:RachealN/product-api.git`
    `cd to the repository`
2. Copy and Update Environment Variables:
   
     Copy the example environment file and update it with your configurations:
    `cp .env.example .env`
    Update `.env` with your MySQL database settings:
   
    `### Database Configuration ###
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0.32&charset=utf8mb4`
    Replace `db_user`, `db_password`, and `db_name` with your MySQL container settings.

4. Configure Docker:
   
    Adjust ports and environment variables in `docker-compose.yml` to match your `.env` settings.

    `version: '3.8'
    services:
      php74-container:
        image: php:7.4-fpm
        volumes:
          - /Users/racheal/code/product-api:/var/www/project
        working_dir: /var/www/project
    
      mysql:
        image: mysql:5.7
        environment:
          MYSQL_ROOT_PASSWORD: root_password
          MYSQL_DATABASE: ${DATABASE_NAME}
          MYSQL_USER: ${MYSQL_USER}
          MYSQL_PASSWORD: ${MYSQL_PASSWORD}`
      
    Note: In services, php74-cointainer, volumes: replace `/Users/racheal/code/product-api` with your path(where your project is located).
    It should be like this: `- /Users/racheal/code/product-api:/var/www/project`

4. Build and Start Docker Containers:
   
    On macOS, you need to open and run the docker desktop application before running the docker commands. On Windows make sure your docker is up and running as well
    `docker-compose build`
    `docker-compose up -d`

  - Use these commands to interact with the docker:
  - To access the container: `docker-compose exec -it <container name> bash`
  - Check the status of the containers: `docker-compose ps`
  - Start the containers by watching their logs: `docker-compose up`
  - Start the containers in the background: `docker-compose up -d`
  - Stop and delete the containers: `docker-compose down`
  - Watch the container logs: `docker-compose logs`

6. Symfony Installation and Configuration:
      You can do both inside the docker container or outside:
    `docker-compose exec -it php74-container bash`
    `composer install`

9. Generate JWT Keys:
     
    Generate the JWT keys required for authentication by running the following commands:
    
    1. `mkdir -p config/jwt`
    2. `openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096`
    3. `openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`

7. Run Migrations:
   
    Run Symfony console commands inside the PHP container to migrate the database schema:
    `php bin/console doctrine:migrations:migrate`
    Ensure migrations run successfully and the database schema is updated.

9. Running the Application:
    
    Start the Symfony local server: `symfony server:start`
    The application should now be up and running.
    NOTE: You can now access the endpoints in Postman, curl, or Insomnia(or any other tool you use in this case)


11. Set up Authentication :
    
    JWT authentication is implemented to secure the endpoints.
    
    To register: 
    `POST /api/register`
    `{
      "email": "example@gmail.com",
      "password": "password"
    }`
    
    
    To login, Login and generate the token:
    `POST /api/login_check` 
    `{
      "username": "example@gmail.com",
      "password": "password"
    }`


    After successfully logging in, you will receive a JSON response containing a token:
    `{
      "token": "your_jwt_token_here"
    }`
    Using the Token for Authorization:
    
    To access protected endpoints, you need to pass the JWT token in the Authorization header of your HTTP requests. Use the Bearer token in Postman or curl:
    
    Authorization: `Bearer your_jwt_token_here`
    
    Example using curl to list all products:
    `curl -X GET "http://localhost:8080/api/products" -H "Authorization: Bearer your_jwt_token_here"`

    Endpoints:
    
    `GET /api/products` - List all products.
    
    `POST /api/products` - Create a new product.
    `{
    "name": "iphone",
    "price": "100000",
    "stock_quantity": 900,
    "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit"
    }`

    `GET /api/products/{id}` - Get details of a single product.
    
    `PUT /api/products/{id}` - Update an existing product.
    `{
     "name": "iphone",
    "price": "100000",
    "stock_quantity": 900,
    "description": "Lorem ipsum dolor sit amet consectetur adipisicing elit"
    }`
    
    `DELETE /api/products/{id}` - Delete a product.

10 . Running Unit Tests:
    
     Run PHPUnit tests for the API endpoints. You can run this either in the Docker container or outside
    `composer test`

11. Run PHPStan and PHP_CodeSniffer:
    
    Execute PHPStan and PHP_CodeSniffer respectively. You can run this either in the Docker container or outside
    `composer phpstan`
    `composer phpcs`

Additional Notes
- MySQL is configured via Docker Compose.
- Set Environment variables properly in the .env file.
- JWT keys are generated and stored securely.
- All dependencies are managed via Composer.
- The application will be run in a Dockerized environment for consistency and ease of setup.

