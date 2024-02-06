# Utilisez un runtime PHP officiel comme image parent
FROM php:8.2-apache

# Installez les extensions PHP et autres dépendances
 RUN apt-get update && \ 
    apt-get install -y libpq-dev && \ 
    docker-php-ext-install pdo pdo_mysql && \
    a2enmod rewrite && \
    service apache2 restart

# Install composer

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiez le code de votre application PHP dans le conteneur
 COPY . .
 
# Exposez le port qu'Apache écoute sur
EXPOSE 80 

# Démarrez Apache lorsque le conteneur exécute
 CMD [ "apache2-foreground" ]