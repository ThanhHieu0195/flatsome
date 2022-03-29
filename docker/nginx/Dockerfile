FROM nginx
LABEL name="flatsomeweb"

WORKDIR /var/www/html
COPY site.conf /etc/nginx/conf.d
COPY project.tar .
RUN apt-get update 
RUN tar -xf project.tar

EXPOSE 81