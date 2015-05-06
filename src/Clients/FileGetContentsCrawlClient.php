<?php namespace Vdbf\SiteMapper\Clients;

use Psr\Log\LogLevel;
use Symfony\Component\DomCrawler\Link;
use Vdbf\SiteMapper\Mapper;
use Vdbf\SiteMapper\Url;
use stdClass;

class FileGetContentsCrawlClient implements CrawlClientInterface
{

    public function linkRequestFactory(Link $link)
    {
        $url = Mapper::stripTrailingSlash($link->getUri());
        return [$url, $url];
    }

    public function linkRequestPooler($requests, Mapper $mapper)
    {
        return array_map(
            function ($request) {
                $response = new stdClass();
                $response->url = $request;
                $response->payload = @file_get_contents($request);
                return $response;
            },
            $requests
        );
    }

    public function linkResponseHandler($response, Mapper $mapper)
    {
        $url = $response->url;

        if ($response->payload !== false) {

            if (!$mapper->isCrawled($url) && $mapper->filterUrl($url)) {

                $mapper->addMapping(new Url($url));
                $mapper->log('UrlWasAddedToMapping ' . $url);

                return [$url, (string)$response->payload];
            }

        } else {

            /** @var \GuzzleHttp\Exception\ConnectException $response */
            $mapper->log('RequestExceptionWasThrown ' . $url, LogLevel::ERROR);
        }

        $mapper->addExclude($url);

        return null;
    }

}