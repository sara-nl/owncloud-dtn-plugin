#!/bin/sh
## UNIX test runner script

errno=0
cd resources/docker

docker-compose up -d
docker exec owncloud-dtn-plugin-unit-tests-runner /bin/bash -c "cd /var/www/owncloud/apps/dtn/tests/unit && php phpunit --colors --debug --bootstrap bootstrap.php --filter 'OCA\\DTN\\Tests' ./"
## save the test exit result in case the tests fail

docker-compose stop

## clean up containers and their volumes
docker rm owncloud-dtn-plugin-unit-tests-runner -v -f
docker rm owncloud-dtn-plugin-unit-tests-runner-db -v -f
docker rm owncloud-dtn-plugin-unit-tests-runner-redis -v -f
