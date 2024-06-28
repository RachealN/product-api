Product API

This project is a RESTful API built using Symfony, designed to manage a list of products. The application provides various endpoints to create, read, update, and delete products and includes JWT authentication for securing the endpoints. The project also includes Docker configuration for easy setup and static code analysis to ensure code quality.

Setup Instructions
Prerequisites
- PHP 7.4 and above
- Docker
- MySQL
- GIT

Step-by-Step Setup
1. Git Clone the Repository
`git clone git@github.com:RachealN/product-api.git`
`cd to the repository`
2. Environment Configuration
   Copy the example environment file and update it with your configurations.
   `cp .env.example .env`
3. Build and Run Docker Containers
   - Ensure Docker is running on your machine.
   - Build and start the containers
     `docker-compose up --build`
4. Install Dependencies
   - Open a new terminal and access the running Symfony container
   `docker exec -it product-api-web-1 bash`
   - Install PHP dependencies using Composer.
     `composer install`
5. Run Migrations
   - Still, inside the Symfony container, run the database migrations to set up the database schema.
     `php bin/console doctrine:migrations: migrate`
6. Generate JWT Keys
   - Generate the JWT keys required for authentication.
     `mkdir -p config/jwt`
     `openssl genpkey -algorithm RSA -out config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096`
     `openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout`
7. Running the Application
   - Start symfony local serve
     `symfony server:start`
   -The application should now be running and accessible at http://localhost:8080
8. Endpoints
   - `GET /api/products` - List all products.
  - `POST /api/products` - Create a new product.
  - `GET /api/products/{id}` - Get details of a single product.
  - `PUT /api/products/{id}` - Update an existing product.
  - `DELETE /api/products/{id}` - Delete a product.
9. Authentication
  - JWT authentication is implemented to secure the endpoints. Register and Login to receive a token:
    To register `POST /api/register`
    `{"email": "example@gmail.com",
    "password": "password"}`
    
    `POST /api/login_check`
    Json format for credentials -
    `{"username": "example@gmail.com",
    "password": "password"}`

10 Running Tests
-Run the tests using the following command inside the Symfony container:
`php vendor/bin/phpunit` or `composer test`

11. Static Code Analysis
    - Run PHPStan: `composer phpstan`
    - Run PHP_CodeSniffer: `composer phpcs`
   
Additional Notes
- MySQL is configured via Docker Compose.
- Set Environment variables properly in the .env file.
- JWT keys are generated and stored securely.
- All dependencies are managed via Composer.
- The application will be run in a Dockerized environment for consistency and ease of setup.

