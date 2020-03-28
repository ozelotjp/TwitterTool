# ozelotjp/TwitterTool

It shows the people you've followed on Twitter and the people you've added to your list in a table format.

In order for this tool to work, you need to register with Twitter as a developer, create an application and get an `API key` and `API secret key` beforehand.

## What you need

- Docker and Docker-Compose
- Your Twitter application (`API key` and `API secret key`)

## How to use

1. `docker-compose run --rm app sh`
2. `composer install`
3. `php -S 0.0.0.0:8000`
4. `cp .env.example .env`
5. If you are using a registered developer account?
    - Yes
        1. Set `CK`, `CS`, `AT` and `AS` values of the `.env` file.
        2. Go to the following page.
        http://localhost:8000
    - No
        1. Set `CK` and `CS` values of the `.env` file.
        2. Get `AT` and `AS` by accessing the following URL.
        http://localhost:8000/login.php
        3. Set `AT` and `AS` values of the `.env` file.
        4. Go to the following page.
        http://localhost:8000/
