echo "Deploy script started"
cd /var/www/gorungo/goback
php artisan down
git pull origin master
composer install --no-dev --prefer-dist
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan migrate --force
php artisan up
echo 'Deploy finished.'
