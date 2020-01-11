# REST API calculator

This service is a set of API commands for such mathematical operations as:
+ Addition of two or three numbers;
+ Finding the difference of two numbers;
+ Multiplication of two numbers.

## Requirements

+ [Docker](https://www.docker.com/products/docker-desktophttp:// "Docker")
+ Any web-browser
+ Web-plugin for testing REST API services


## Install
To deploy project, open a terminal and enter the following command:

    docker pull nraywill/raycalc-api

Docker will download Docker-image from [hub](https://hub.docker.com/r/nraywill/raycalc-api "hub").

## Usage

Enter the following command in a terminal window:

	docker run -it -p 8009:8000 nraywill/raycalc-api

This command will start Docker-container and built-in Symfony server  with port 8000 forwarding to 8009 of the external machine. After that servis will be available at:
http://localhost:8009

***GET***


    http://localhost:8009/calc/add?A=123&B=456
    http://localhost:8009/calc/add?A=123&B=456&C=789
    http://localhost:8009/calc/sub?A=123&B=456
    http://localhost:8009/calc/mul?A=123&B=456

***POST***

For post-testing you should use one of the follow adresses with parameters A, B and C (only for additon). Type of request boty should be "URLencoded form data".

    http://localhost:8009/calc/add
    http://localhost:8009/calc/sub
    http://localhost:8009/calc/mul

## Documentation

With started Docker-container swagger-documentation will be available at
http://localhost:8009/docs

## Test

For functional and unit testing, in the terminal window with the Docker-container interactively launched, enter the appropriate command:

***Functional***

    ./vendor/bin/codecept run functional

***Unit***

    ./vendor/bin/codecept run unit
or just 

    ./vendor/bin/codecept run
for both of them.