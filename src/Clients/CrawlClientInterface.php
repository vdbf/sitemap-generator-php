<?php namespace Vdbf\SiteMapper\Clients;

use Symfony\Component\DomCrawler\Link;
use Vdbf\SiteMapper\Mapper;

interface CrawlClientInterface
{

    /**
     * Convert Link to Request instances
     * @param Link $link
     * @return mixed
     */
    public function linkRequestFactory(Link $link);

    /**
     * Pool requests
     * @param array $requests
     * @return mixed
     */
    public function linkRequestPooler($requests, Mapper $mapper);

    /**
     * Handle response
     * @param \GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Exception\RequestException $response
     * @param Mapper $mapper
     * @return mixed
     */
    public function linkResponseHandler($response, Mapper $mapper);

}