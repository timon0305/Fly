php composer.phar install
php app/console assets:install web
php app/console  doctrine:schema:update --force

php app/console  doctrine:fixtures:load

sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX app/cache app/logs
