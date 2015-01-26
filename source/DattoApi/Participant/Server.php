<?php

namespace DattoApi\Participant;

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

        $server = new JsonRpc();
        $output = $server->evaluate($json);

        if ($output === null) {
            self::successNoContent();
        }

        self::successContent($output);
    }

    private static function errorInvalidContentType()
    {
        header('HTTP/1.0 400 Bad Request');
        echo self::getErrorPage(400, "Please submit your request with the HTTP header:<br>\r\n&ldquo;Content-Type: application/json&rdquo;");
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
        header('Content-Type: application/json');
        echo $json;
        exit();
    }

    private static function getErrorPage($code, $message)
    {
        $title = htmlspecialchars("Error {$code}");
        $description = $message;

        return <<<EOS
<!DOCTYPE html>

<html lang="en">

<head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
 <title>{$title}</title>
 <style type='text/css'>
* { margin:0; padding:0; }
body { margin:12.5%; font-family:Georgia,serif; line-height:1.5em; color:#333; }
h1 { font-size:2em; font-weight:normal; padding:0 0 .75em 0; }
 </style>
</head>

<body>

<h1>{$title}</h1>

<p>{$description}</p>

</body>

</html>
EOS;

    }
}
