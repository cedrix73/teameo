FROM php:7.4-fpm
LABEL Name=teamplanning Version=0.0.1 Author=cedric73
ENV TEAM_DATABASE_SERVER localhost
ENV TEAM_DATABASE_USER root
ENV TEAM_DATABASE_PASSWORD cedrix
ENV TEAM_DATABASE_NAME team_planning
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \ 
    libpq-dev \ ning_d
# Clear cache
    libxml2-dev \ 
    libpq-dev \ ning_d
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# RUN apt-get update
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install pdo_pgsql
WORKDIR /home/teamPlanning
EXPOSE 80
CMD ["/app/docker/start.sh"]
# docker build Dockerfile -t [Cedrix73]/[teamplanning]