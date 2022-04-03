<?php

/**
 * Test: BulkGate\Message\Request
 * @author Lukáš Piják 2018 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace Test;

use BulkGate;
use Tester;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$header = <<<EOT
HTTP/1.1 200 OK
Server: nginx
Date: Fri, 02 Feb 2018 12:48:02 GMT
Content-Type: text/html; charset=utf-8
Connection: close
X-Powered-By: Nette Framework
X-Frame-Options: SAMEORIGIN
Vary: X-Requested-With
EOT;

$headers = new BulkGate\Message\HttpHeaders($header);

Assert::equal('HTTP/1.1 200 OK', $headers->getHeader('http_code'));

Assert::equal('nginx', $headers->getHeader('Server'));
Assert::equal('nginx', $headers->getHeader('server'));

Assert::equal('text/html; charset=utf-8', $headers->getHeader('content-type'));

Assert::equal('text/html', $headers->getContentType());

Assert::equal([
     "http_code" => "HTTP/1.1 200 OK",
      "server" => "nginx",
      "date" => "Fri, 02 Feb 2018 12",
      "content-type" => "text/html; charset=utf-8",
      "connection" => "close",
      "x-powered-by" => "Nette Framework",
      "x-frame-options" => "SAMEORIGIN",
      "vary" => "X-Requested-With",
], $headers->getHeaders());

$headers = new BulkGate\Message\HttpHeaders(implode("\r\n", [
    "HTTP/1.1 200 OK",
    "Server: nginx"
]));

Assert::equal('HTTP/1.1 200 OK', $headers->getHeader('http_code'));

Assert::equal('nginx', $headers->getHeader('Server'));
Assert::equal('nginx', $headers->getHeader('server'));

Assert::equal('', $headers->getContentType());

Assert::equal([
    "http_code" => "HTTP/1.1 200 OK",
    "server" => "nginx",
], $headers->getHeaders());
