FROM mysql
LABEL name="flatsomedb"

ENV MYSQL_DATABASE=datviet
ENV MYSQL_ROOT_PASSWORD=p@ssw0rd123
ADD dump.sql /docker-entrypoint-initdb.d

EXPOSE 3306