<?php namespace Vdbf\SiteMapper;

use SimpleXMLElement;
use DateTime;

final class Url
{

    /**
     * @var string
     */
    private $loc;

    /**
     * @var DateTime|null
     */
    private $lastmod;

    /**
     * @var string|null
     */
    private $changefreq;

    /**
     * @var double|null
     */
    private $priority;

    /**
     * @param $loc
     * @param DateTime $lastmod
     * @param null $changefreq
     * @param null $priorty
     */
    public function __construct($loc, DateTime $lastmod = null, $changefreq = null, $priorty = null)
    {
        $this->loc = $loc;
        $this->lastmod = $lastmod;
        $this->changefreq = $changefreq;
        $this->priority = $priorty;
    }

    /**
     * @return string
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @return DateTime|null
     */
    public function getLastmod()
    {
        return $this->lastmod;
    }

    /**
     * @return string|null
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * @return double|null
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
            $url->addChild('lastmod', $this->getLastmod()->format(DateTime::ATOM));
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