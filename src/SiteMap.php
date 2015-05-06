<?php namespace Vdbf\SiteMapper;

use SimpleXMLElement;

final class SiteMap
{

    /**
     * @var array
     */
    protected $mapping;

    /**
     * @param array $data
     */
    public function __construct($mapping = [])
    {
        $this->mapping = $mapping;
    }

    /**
     * @param null $filename
     * @return mixed
     */
    public function toXml($filename = null)
    {
        $element = new SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        /** @var Url $value */
        foreach ($this->mapping as $value) {
            $value->appendToSimpleXML($element);
        }

        return is_null($filename) ? $element->asXML() : $element->asXML($filename);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toXml();
    }

}