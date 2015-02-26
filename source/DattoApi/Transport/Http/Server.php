<?php

namespace DattoApi\Transport\Http;

use DattoApi\Data\JsonRpc;

/**
 * Class Server
 *
 * @link http://www.simple-is-better.org/json-rpc/transport_http.html Proposed specifications
 *
 * @package DattoApi\Transport\Http
 */
class Server
{
    public function run()
    {
        if (@$_SERVER['CONTENT_TYPE'] !== 'application/json') {
            self::errorInvalidContentType();
        }

        $contents = @file_get_contents('php://input');

        if ($contents === false) {
            self::errorInvalidBody();
        }

        $server = new JsonRpc();
        $output = $server->evaluate($contents);

        if ($output === null) {
            self::successNoContent();
        }

        self::successContent($output);
    }

    private static function errorInvalidContentType()
    {
        self::error(415, 'Unsupported Media Type', "Please submit your request with the HTTP header:<br>\r\n&ldquo;Content-Type: application/json&rdquo;");
    }

    private static function errorInvalidBody()
    {
        self::error(400, 'Bad Request', "Unable to read the HTTP body.");
    }

    private static function successNoContent()
    {
        header('HTTP/1.0 204 No Content');
        exit();
    }

    private static function successContent($content)
    {
        header('HTTP/1.0 200 OK');
        header('Content-Type: application/json');
        header('Content-Length: ' . strlen($content));
        echo $content;
        exit();
    }

    private static function error($code, $title, $description)
    {
        header("HTTP/1.0 {$code} {$title}");

        echo <<<EOS
<!DOCTYPE html>

<html lang="en">

<head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
 <title>Error {$code}: {$title}</title>
 <style type='text/css'>
* { margin:0; padding:0; }
body {
    margin: 12.5%;
    font-family: Georgia,serif;
    line-height: 1.5em;
    color: #333;
}

header {
    margin: 0 0 1.5em 0;
}

h1 {
    font-size: 2em;
    padding: 0 0 .25em 0;
}

header p {
    font-size: 1.2599em;
    color: #666;
    font-style: italic;
    text-transform: lowercase;
}
 </style>
</head>

<body>

<header>
    <h1>Error {$code}</h1>
    <p>{$title}</p>
</header>

<p>{$description}</p>

</body>

</html>
EOS;

        exit();
    }
}
