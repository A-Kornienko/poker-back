<?php

// config/services.php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container): void {
    /*$mysql_login = getenv('MYSQL_LOGIN');
    $mysql_password = getenv('MYSQL_PASSWORD');
    $database = getenv('DATABASE');
    $hostname = getenv('HOSTNAME');*/

    $mysqlLogin    = getenv('POKER_MYSQL_LOGIN') ?: env('POKER_MYSQL_LOGIN');
    $mysqlPassword = getenv('POKER_MYSQL_PASSWORD') ?: env('POKER_MYSQL_PASSWORD');
    $database      = getenv('POKER_MYSQL_DATABASE') ?: env('POKER_MYSQL_DATABASE');
    $hostname      = getenv('APP_MYSQL_HOSTNAME') ?: env('APP_MYSQL_HOSTNAME');

    $container->import('_services.yaml');

    $container->parameters()
        // the parameter name is an arbitrary string (the 'app.' prefix is recommended
        // to better differentiate your parameters from Symfony parameters).
        ->set('app.database_url', "mysql://$mysqlLogin:$mysqlPassword@$hostname:3306/$database?serverVersion=8.0.32&charset=utf8mb4");
};
