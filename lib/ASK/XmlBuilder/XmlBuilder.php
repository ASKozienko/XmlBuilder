<?php

class XmlBuilder extends XmlElement
{
    /**
     * @var bool
     */
    protected $formatOutput;

    public function __construct($version = '1.0', $encoding = 'utf-8', $formatOutput = false)
    {
        $this->dom = new \DOMDocument($version, $encoding);
        $this->formatOutput = (bool) $formatOutput;
        $this->current = $this->dom;
    }

    /**
     * @param bool $formatOutput
     *
     * @return string
     */
    public function getXml($formatOutput = null)
    {
        $this->dom->formatOutput = is_bool($formatOutput) ? $formatOutput : $this->formatOutput;

        return $this->dom->saveXML();
    }

    /**
     * @param string $filename
     * @param bool $formatOutput
     *
     * @return bool
     */
    public function save($filename, $formatOutput = null)
    {
        $this->dom->formatOutput = is_bool($formatOutput) ? $formatOutput : $this->formatOutput;

        return false !== $this->dom->save($filename);
    }
}
