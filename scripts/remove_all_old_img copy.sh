#!/bin/bash
docker image rm $(docker image ls|grep flat|awk '{print $1}')