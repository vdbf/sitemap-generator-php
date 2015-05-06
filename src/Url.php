<?php namespace Vdbf\SiteMapper;

use SimpleXMLElement;

final class Url
{

    /**
     * @var
     */
    private $loc;

    /**
     * @var null
     */
    private $lastmod;

    /**
     * @var null
     */
    private $changefreq;

    /**
     * @var null
     */
    private $priority;

    public function __construct($loc, $lastmod = null, $changefreq = null, $priorty = null)
    {
        $this->loc = $loc;
        $this->lastmod = $lastmod;
        $this->changefreq = $changefreq;
        $this->priority = $priorty;
    }

    /**
     * @return mixed
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @return null
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * @return null
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * @return null
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Equality comperator
     * @param $uri
     * @return bool
     */
    public function equals($uri)
    {
        return $uri instanceof Url && (string)$uri == $this->__toString();
    }

    /**
     * @param SimpleXMLElement $element
     */
    public function appendToSimpleXML(SimpleXMLElement &$element)
    {
        $url = $element->addChild('url');

        $url->addChild('loc', $this->getLoc());

        if ($this->getLastmod()) {
            $url->addChild('lastmod', $this->getLastmod());
        }

        if ($this->getChangefreq()) {
            $url->addChild('changefreq', $this->getChangefreq());
        }

        if ($this->getPriority()) {
            $url->addChild('priority', $this->getPriority());
        }
    }

    /**
     * Returns the location property of the uri
     * @return mixed
     */
    public function __toString()
    {
        return $this->loc;
    }

}