<?php namespace Vdbf\SiteMapper\Clients;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Pool;
use Psr\Log\LogLevel;
use Symfony\Component\DomCrawler\Link;
use Vdbf\SiteMapper\Mapper;
use Vdbf\SiteMapper\Url;

class GuzzleCrawlClient implements CrawlClientInterface
{

    /**
     * @var ClientInterface
     */
    protected $guzzle;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param ClientInterface $guzzle
     * @param array $options
     */
    public function __construct(ClientInterface $guzzle, $options = [])
    {
        $this->guzzle = $guzzle;
        $this->options = $options;
    }

    /**
     * @param Link $link
     * @return array
     */
    public function linkRequestFactory(Link $link)
    {
        $url = Mapper::stripTrailingSlash($link->getUri());
        return [$url, $this->guzzle->createRequest('GET', $url)];
    }

    /**
     * @param array $requests
     * @param Mapper $mapper
     * @return \GuzzleHttp\BatchResults
     */
    public function linkRequestPooler($requests, Mapper $mapper)
    {
        return Pool::batch($this->guzzle, $requests);
    }

    /**
     * @param $response
     * @param Mapper $mapper
     * @return array
     */
    public function linkResponseHandler($response, Mapper $mapper)
    {
        if ($response instanceof ResponseInterface && $response->getStatusCode() == 200) {

            $url = Mapper::stripTrailingSlash($response->getEffectiveUrl());

            //recheck uri because it might be redirected
            if (!$mapper->isCrawled($url) && $mapper->filterUrl($url)) {

                $mapper->addMapping(new Url($url, $response->getHeader('Last-Modified')));
                $mapper->log('UrlWasAddedToMapping ' . $url);

                return [$url, (string)$response->getBody()];

            }

        } else {

            $url = Mapper::stripTrailingSlash($response->getRequest()->getUrl());

            /** @var \GuzzleHttp\Exception\ConnectException $response */
            $mapper->log($response->getMessage(), LogLevel::ERROR);
        }

        $mapper->addExclude($url);

        return null;
    }

}