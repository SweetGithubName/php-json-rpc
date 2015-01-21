<?php

namespace DattoApi\Oauth2;

use DattoApi\JsonRpc;

class Server
{
    public function run()
    {
        if (@$_SERVER['CONTENT_TYPE'] !== 'application/json') {
            self::errorInvalidContentType();
        }

        $json = @file_get_contents('php://input');

        if ($json === false) {
            self::errorInvalidBody();
        }

        $server = new JsonRpc\Server();
        $output = $server->evaluate($json);

        if ($output === null) {
            self::successNoContent();
        }

        self::successContent($output);
    }

    private static function errorInvalidContentType()
    {
        header('HTTP/1.0 400 Bad Request');
        echo "Please submit your request with the HTTP header:<br>Content-type: application/json";
        exit();
    }

    private static function errorInvalidBody()
    {
        header('HTTP/1.0 400 Bad Request');
        echo "Unable to read the HTTP body";
        exit();
    }

    private static function successNoContent()
    {
        header('HTTP/1.0 204 No Content');
        exit();
    }

    private static function successContent($json)
    {
        header('HTTP/1.0 200 OK');
        header('Content-type: application/json');
        echo $json;
        exit();
    }
}
