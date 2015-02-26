<?php

require_once dirname(__DIR__) . '/autoload.php';

spl_autoload_register('autoloadSource');
spl_autoload_register('autoloadExample');

use DattoApi\Transport\Http\Client;
use DattoApi\Data\Message\Query;

$client = new Client('http://api/server.php');

$query = new Query(1, 'Example/Math/subtract', array(3, 2));
$reply = $client->send($query);

echo json_encode($reply), "\n";

// /etc/hosts
/*
127.0.0.1   localhost api
*/

// ln -s /etc/apache2/sites-available /etc/apache2/sites-enabled
// /etc/apache2/sites-available
/*
<VirtualHost *:80>
    ServerName api

    ServerAdmin webmaster@localhost
    DocumentRoot /home/username/Projects/datto-api/datto-api/examples/public

    <Directory /home/username/Projects/datto-api/datto-api/examples/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
*/