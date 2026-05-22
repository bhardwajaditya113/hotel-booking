web: vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --force && php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
