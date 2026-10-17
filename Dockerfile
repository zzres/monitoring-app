FROM php:8.2-apache

#Installer les dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# Installer les dépendances système nécessaires
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Activer le module Apache pour les URLs propres de Laravel
RUN a2enmod rewrite

# Pointer Apache vers le dossier public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html 

# Copier tout le projet
COPY . .

# Installer les dépendances PHP (sans les paquets de développement, pour la production)
RUN composer install --optimize-autoloader --no-dev 

# Installer les dépendances JS et compiler les assets
RUN npm install && npm run build

# Donner les bonnes permissions aux dossiers que Laravel doit pouvoir écrire
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 

# Copier le script de démarrage et le rendre exécutable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh 
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]