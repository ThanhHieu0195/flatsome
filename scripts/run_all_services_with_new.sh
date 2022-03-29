#!/bin/bash
docker-compose down -d
docker image rm $(docker image ls|grep flat|awk '{print $1}')
docker-compose up -d