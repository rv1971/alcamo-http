# Usage examples

## FileResponse

~~~
use alcamo\http\FileResponse;

$response = FileResponse::newFromPath('/srv/www/htdocs/foo.xml');

$response->emit();
~~~

This sends an HTTP response with `Content-Type` and `Content-Length`
headers. It may be more efficient than doing the same thing simply
with `Laminas\Diactoros\Response` because is sends the file via
`fpassthru()`, without need to keep the entire file content in memory.

## PipeStream

~~~
use alcamo\http\{PipeStream, Response};
use alcamo\process\OutputProcess;

$stream = new PipeStream(new OutputProcess('convert foo.png jpeg:-'));

$response = new Response(
    [ [ 'dc:format', 'image/jpeg' ] ],
    $stream
);

$response->emit();
~~~

This sends an HTTP response with `Content-Type` header. Again, it may
be more efficient than doing the same thing simply with
`Laminas\Diactoros\Response` because is sends the file via
`fpassthru()`, without need to keep the entire file content in memory.

The exmaple also illustrates a possible use of the Response class,
giving RDFa metadata which is automatically converted into HTTP
headers.

## Simple status page

~~~
use alcamo\http\Response;

$response = Response::newFromStatusAndText(403);

$response->emit();
~~~

This will send an HTTP response with `Content-Type: text/plain` and
`Content-Length` header, with status code 403 and the text
'Forbidden'.
