<?php
use DevCoder\DotEnv;

// Loading environment variables
(new DotEnv(__DIR__ . '/.env'))->load();

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
       'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => getenv('DATABASE_HOST'),
            'name' => getenv('DATABASE_NAME'),
            'user' => getenv('DATABASE_USERNAME'),
            'pass' => getenv('DATABASE_PASSWORD'),
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
