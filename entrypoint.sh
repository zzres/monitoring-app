#!/bin/bash

# Mettre en cache la configuration pour de meilleur performances
php artisan config:cache

# Lancer les migrations automatiquement au démarrage
php artisan migrate --force
php artisan db:seed --force

# Le scheduler tourne en arrière-plan, sous l'utilisateur d'Apache
runuser -u www-data -- php artisan schedule:work &

# Démarrer Apache au premier plan
apache2-foreground