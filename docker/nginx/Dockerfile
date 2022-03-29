FROM nginx
LABEL name="flatsomeweb"

WORKDIR /var/www/html
COPY site.conf /etc/nginx/conf.d
RUN apt-get update
RUN apt-get install gzip -y
COPY project.tar.gz .
RUN gunzip project.tar.gz
RUN tar -xf project.tar
RUN rm -rf project.tar
EXPOSE 81