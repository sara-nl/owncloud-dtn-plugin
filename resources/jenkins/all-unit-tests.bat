:: Windows test runner script

SETLOCAL ENABLEEXTENSIONS ENABLEDELAYEDEXPANSION
SET /A errno=0

:: switch to location of docker-compose file
cd resources/docker

docker-compose up -d
docker exec owncloud-dtn-plugin-unit-tests-runner /bin/bash -c "cd /var/www/owncloud/apps/dtn/tests/unit && php phpunit --colors --debug --bootstrap bootstrap.php --filter 'OCA\\DTN\\Tests' ./"
:: save the test exit result in case the tests fail
IF /I "%ERRORLEVEL%" NEQ "0" ( 
    SET /A errno=%ERRORLEVEL%
)
docker-compose stop

:: clean up containers and their volumes
docker rm owncloud-dtn-plugin-unit-tests-runner -v -f
docker rm owncloud-dtn-plugin-unit-tests-runner-db -v -f
docker rm owncloud-dtn-plugin-unit-tests-runner-redis -v -f
IF /I "%ERRORLEVEL%" NEQ "0" ( 
    SET /A errno=%ERRORLEVEL%
)
:: exit with the test result
Exit /B %errno%