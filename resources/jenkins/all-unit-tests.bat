SETLOCAL ENABLEEXTENSIONS ENABLEDELAYEDEXPANSION
SET /A errno=0

cd resources/docker
::cd c:\xampp\htdocs\ownCloud-DTN-plugin\resources\docker
docker-compose up -d
docker exec owncloud-dtn-plugin-unit-tests-runner /bin/bash -c "cd /var/www/owncloud/apps/dtn/tests/unit && php phpunit --colors --debug --bootstrap bootstrap.php --filter 'OCA\\DTN\\Tests' ./"
IF /I "%ERRORLEVEL%" NEQ "0" ( 
    SET /A errno=%ERRORLEVEL%
)
docker-compose stop
IF /I "%ERRORLEVEL%" NEQ "0" ( 
    SET /A errno=%ERRORLEVEL%
)
Exit /B %errno%