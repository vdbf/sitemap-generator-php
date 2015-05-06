<?php namespace Vdbf\SiteMapper;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;
use Vdbf\SiteMapper\Clients\CrawlClientInterface;
use Vdbf\SiteMapper\Support\LoggerHelperTrait;

class Mapper implements LoggerAwareInterface
{

    use LoggerAwareTrait, LoggerHelperTrait;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var CrawlClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $mapping = [];

    /**
     * @var array
     */
    protected $excludes = [];

    /**
     * @param array $options
     */
    protected function __construct($options = [])
    {
        $this->options = $options;
    }

    /**
     * @param CrawlClientInterface $client
     */
    public function setClient(CrawlClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $url
     * @return Mapper
     */
    public static function url($url, $options = [])
    {
        preg_match('/^https{0,1}:\/\/([.\w]+)/', $url, $matches);

        $url = Mapper::stripTrailingSlash($url);

        $domain = isset($matches) ? $matches[1] : $url;

        return new Mapper($options + compact('url', 'domain'));
    }

    /**
     * Turn a url into a crawler instance
     * @param $url
     * @param $content
     * @return Crawler
     */
    protected function getDomCrawler($url, $content = null)
    {
        $dom = $content ?: file_get_contents($url);

        return new Crawler($dom === false ? '' : $dom, $url, $this->options['url']);
    }

    /**
     * Start crawling
     * @return SiteMap
     */
    public function crawl()
    {
        $this->crawlUrl(Mapper::stripTrailingSlash($this->options['url']));

        $mapping = new SiteMap($this->mapping);

        $this->clearMapping();
        $this->clearExclude();

        return $mapping;
    }

    /**
     * @param $url
     * @param $mapping
     */
    protected function crawlUrl($url, $content = null)
    {
        //fetch content of url into a crawler
        $resource = $this->getDomCrawler($url, $content);

        //scan for links on the resource that have a valid url
        $links = array_filter(
            $resource->filterXPath('//a')->links(),
            function (Link $link) {
                $url = static::stripTrailingSlash($link->getUri());
                return !$this->isCrawled($url) && $this->filterUrl($url);
            }
        );

        $this->crawlLinks($links);
    }

    /**
     * @param $links
     */
    protected function crawlLinks($links)
    {
        $added = $this->handleLinksByClient($links);

        //after all links have been added, crawl them (recursion)
        foreach ($added as $url => $content) {
            $this->crawlUrl($url, $content);
        }
    }

    /**
     * @param $links
     * @return array
     */
    protected function handleLinksByClient($links)
    {
        $added = [];
        $requests = [];

        //turn Link instances into requests the pooler can handle
        foreach ($links as $link) {

            list($url, $request) = $this->client->linkRequestFactory($link);

            $requests[$url] = $request;

        }

        //pool requests and execute them as a batch
        $responses = $this->client->linkRequestPooler($requests, $this);

        //handle responses synchronously
        foreach ($responses as $response) {

            if (is_null($handledResponse = $this->client->linkResponseHandler($response, $this))) continue;

            list($url, $content) = $handledResponse;

            $added[$url] = $content;
        }

        return $added;
    }

    /**
     * @param $url
     * @return bool
     */
    public function filterUrl($url)
    {
        //filter hashbangs, alternate views, filters and email addresses
        return preg_match('/^((?!(view=|filter=|\#|\@)).)*$/', $url) && strpos($url, $this->options['domain']) !== false;
    }

    /**
     * @param $input
     * @return string
     */
    public static function stripTrailingSlash($input)
    {
        return strpos(strrev($input), '/') === 0 ? substr($input, 0, -1) : $input;
    }

    /**
     * @param $url
     * @return bool
     */
    public function isCrawled($url)
    {
        return isset($this->mapping[(string)$url]) || isset($this->excludes[(string)$url]);
    }

    /**
     * @param $url
     */
    public function addMapping($url)
    {
        $this->mapping[(string)$url] = $url instanceof Url ? $url : new Url($url);
    }

    /**
     * Clear mapping
     */
    public function clearMapping()
    {
        $this->mapping = [];
    }

    /**
     * @param $url
     */
    public function addExclude($url)
    {
        $this->excludes[(string)$url] = true;
    }

    /**
     * Clear excludes
     */
    public function clearExclude()
    {
        $this->excludes = [];
    }
}