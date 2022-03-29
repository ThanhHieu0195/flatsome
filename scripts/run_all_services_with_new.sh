#!/bin/bash
echo "BE CAREFUL! this command will delete all data on mysql"
echo "Did you backup it ?"
echo "For safe system will auto backup it"
./scripts/backup_db.sh
sleep 2

docker image rm $(docker image ls|grep flat|awk '{print $1}')
docker-compose up -d