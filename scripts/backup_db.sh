#!/bin/bash
dateNow=$(date +"%d%m%y")
timeNow=$(date +"%H%M_%S")

backupPath="backup/$dateNow/$timeNow"
if [ ! -d "backup/$dateNow" ];
then
    mkdir "backup/$dateNow"
fi

if [ ! -d "backup/$dateNow/$timeNow" ];
then
    mkdir "backup/$dateNow/$timeNow"
fi

name=dump
uploadName=uploads

docker exec -it flatsomes_flatsomedb_1 bash -c "mysqldump -u root -pp@ssw0rd123 datviet" > $name.sql
echo "cloned dump for mysql"
tar -cf $name.tar $name.sql
gzip $name.tar
rm -rf $name.sql
mv $name.tar.gz $backupPath/
echo "backup dump for mysql"


docker exec -it flatsomes_flatsomeweb_1 bash -c "cd /var/www/html/project/wp-content; tar -cf uploads.tar uploads/; gzip uploads.tar; mv uploads.tar.gz /root/"
echo "cloned dump for uploads"

docker cp flatsomes_flatsomeweb_1:/root/uploads.tar.gz .
mv uploads.tar.gz $backupPath
echo "backup dump for uploads"

rm -rf *gz
rm -rf *tar
rm -rf *sql

echo "saved to $backupPath/$name.tar.gz"
echo "saved to $backupPath/$uploadName.tar.gz"
