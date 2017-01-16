<?php
define('PREVENT_BOOTSTRAPPING', true);
require_once realpath(dirname(__FILE__)) . '/public/index.php';

return array(
    'paths' => array(
        'migrations' => APPLICATION_PATH . '/../migrations',
    ),
    'environments' => array(
        'default_migration_table' => 'phinx_log',
        'default_database' => 'default',
        'default' => array(
            'adapter' => 'mysql',
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USERNAME,
            'pass' => DB_PASSWORD,
            'port' => '3306',
        )
    )
);
