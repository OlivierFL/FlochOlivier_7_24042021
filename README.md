# BileMo Rest API

[![Maintainability](https://api.codeclimate.com/v1/badges/fce55f925a496109de14/maintainability)](https://codeclimate.com/github/OlivierFL/FlochOlivier_7_24042021/maintainability)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=OlivierFL_FlochOlivier_7_24042021&metric=alert_status)](https://sonarcloud.io/dashboard?id=OlivierFL_FlochOlivier_7_24042021)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=OlivierFL_FlochOlivier_7_24042021&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=OlivierFL_FlochOlivier_7_24042021)

## Project

Project 7 - Rest API built with Symfony Framework.

## Requirements

Mandatory :

- `PHP >= 8.0`
- `Symfony CLI`
- `openssl` ([needed to generate keys for JWT authentication](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#generate-the-ssl-keys))

Optional :

- `Make` to use the [_Makefile_](./Makefile) and custom commands
- `Docker` and `Docker-compose` for _MySQL_ database and _PhpMyAdmin_ containers

Unit Tests :

- `PHPUnit`

## Installation

1. To get this project on your local machine, simply clone this repository :
   ```shell
   git clone git@github.com:OlivierFL/FlochOlivier_7_24042021.git
   ```


2. Install the dependencies :

- `composer install`


3. Environment configuration :

   To configure local dev environment, create a `.env.local` file at the root of the project.

   To configure database connection, override the `DATABASE_URL` env variable with your database credentials and database name, for example :

    ```dotenv
    DATABASE_URL="mysql://root:root@127.0.0.1:3306/bilemo?serverVersion=5.7"
    ```

   If you're using the _MySQL_ Docker container, the config is :

    ```dotenv
    DATABASE_URL="mysql://root:admin@127.0.0.1:3306/bilemo"
    ```

   In the `.env` file, 2 variables are available, to configure the default number of products and users for pagination, and the directory for images uploads (default directory is `/public/uploads` at the root of the project) :

    ```dotenv
    LIMIT=10
    UPLOADS_DIRECTORY=/public/uploads
    ```

4. After configuring the database connection, run `bin/console doctrine:database:create` to create the database.


5. Then run `bin/console doctrine:fixtures:load` to load the example data into the database. If you're using _Docker_, _PhpMyAdmin_ is available on `localhost:8080` (_user_ : `root`, _password_ : `admin`).


6. Generate the ___SSL keys___ needed to handle the JWT authentication : `bin/console lexik:jwt:generate-keypair`.


7. Start the Symfony server with `symfony server:start`.

The ___base url___ for the API is : `localhost:8000/api`.

## Usage

List of useful commands to use the project :

- `symfony server:start` to start the Symfony server
- `symfony server:stop` to stop the Symfony server

Commands to use with _Docker_ and _Make_ (commands are available in _Makefile_ at the root of the project) :

- `make up` to start _Docker_ stack (_MySQL_ and _PhpMyAdmin_) and _Symfony server_
- `make install` to run installation process automatically (manual [environment configuration](#Installation) is needed __before__ running this command)
- `make tests` to run _PHPUnit_ tests
- `make down` to stop _Docker_ stack (_MySQL_ and _PhpMyAdmin_) and _Symfony server_

## Sample data

In order to have a fully functional API, the fixtures contains :

- 2 Company that can access the API :
    - a Company with __Company__ _username_, and __Company1234__ _password_.

    - another Company with __Company Test__ _username_, and __1234Company__ _password_.


- A default list of Products (50 items). Each Product is related to a Brand (10 items).


- A default list of Users (10 items). Each User is related to one of the two Company.

## API Documentation

The documentation for the API is accessible at : `localhost:8000/api/doc`.

## Third party libraries

Packages and bundles used in this project :

- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle) to handle JWT authentication
- [BazingaHateoasBundle](https://github.com/willdurand/BazingaHateoasBundle) to implement HATEOAS representations
- [NelmioApiDocBundle](https://github.com/nelmio/NelmioApiDocBundle) to generate API documentation
- [RollerWorks PasswordStrengthBundle](https://github.com/rollerworks/PasswordStrengthBundle)
- [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle)

## Docker (optional)

This project uses Docker for _MySQL_ database and _PhpMyAdmin_.

The stack is composed of 2 containers :

- mysql
- phpMyAdmin

The configuration is available in the [docker-compose.yaml](./docker-compose.yaml).

## Tests

_PhpUnit_ is used to run the tests.

In a terminal, at the root of the project, run `vendor/bin/phpunit` or `make tests`.

## Code quality

Links to code quality tools used for this project:

Codeclimate : https://codeclimate.com/github/OlivierFL/FlochOlivier_7_24042021

SonarCloud : https://sonarcloud.io/dashboard?id=OlivierFL_FlochOlivier_7_24042021
