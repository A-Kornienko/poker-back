## Install Poker app
1. Create folder for your project, folder name should be `poker2`.
2. Run `git clone git@github.com:GamblingSoft/poker.git` into directory `poker2`.
3. Go out of the directory `poker2`
4. Install laradock from this manual https://laradock.io/getting-started/ use setup for multiple projects configuration.
5. Directory `laradock` should be located near the `poker2`, on the same level.
6. Update `MYSQL_VERSION=8.0` in `laradock/.env`.
7. Update `PHP_VERSION=8.2` in `laradock/.env`.
8. Only for `Mac with M1` processor, make sure that this settings is added `APACHE_FOR_MAC_M1=true` in `laradock/.env`.
8. Run `docker compose up -d mysql apache2` from `laradock` folder.
9. Wait for build with default configuration for apache2 and mysql.
10. Copy `.env.dist` to `.env`.
11. Create local database `poker`.
12. Run `composer install` inside the `poker2` directory or inside the container.
13. Run migrations `php bin/console doctrine:migrations:migrate`.
14. Copy `public/engine/mini_example.php` to `public/engine/mini.php` it's a stub for core classes.
15. Run `php bin/console importmap:install`.
16. Copy `assets` to directory `public`.

### Troubleshootings: 
1. You can add some domain name to hosts:
 - Linux/Mac: 
    * `etc/hosts` -> add this row `127.0.0.1 {domain_name}`, where {domain_name} it's your local domain for access from browser.
 - Windows:
    * `c:\Windows\System32\Drivers\etc\hosts` -> add this row `127.0.0.1 {domain_name}`, where {domain_name} it's your local domain for access from browser.
    * to modify file `hosts` from Windows, you need to open nodepad from administator and then update and save file `hosts`.

2. For Mysql error please try to set `DATA_PATH_HOST=~/.laradock/` in `laradock/.env`.
3. Make sure that `VPN is disabled`.