<?php

namespace ASK\XmlBuilder;

class XmlBuilder
{
    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @var \DOMDocument|\DOMElement
     */
    private $current;

    /**
     * @param string $version
     * @param string $encoding
     * @param bool   $formatOutput
     *
     * @return XmlBuilder
     */
    public static function create($version = '1.0', $encoding = 'utf-8', $formatOutput = false)
    {
        $dom = new \DOMDocument($version, $encoding);
        $dom->formatOutput = (bool) $formatOutput;

        return new static($dom);
    }

    /**
     * @param \DOMNode $node
     */
    private function __construct(\DOMNode $node)
    {
        $this->current = $node;
        $this->dom = $node instanceof \DOMDocument ? $node : $node->ownerDocument;
    }

    /**
     * Assigns $name to new XmlElement which holds pointer to current XML element
     *
     * @param null $name
     *
     * @return $this
     */
    public function reference(&$name)
    {
        $name = new static($this->current);

        return $this;
    }

    /**
     * @return $this
     */
    public function end()
    {
        if (null === $this->current->parentNode) {
            throw new \LogicException('Could not find parent node');
        }

        $this->current = $this->current->parentNode;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $namespace
     *
     * @return $this
     */
    public function element($name, $value = null, $namespace = null)
    {
        $element = $namespace
            ? $this->dom->createElementNS($namespace, $name)
            : $this->dom->createElement($name)
        ;

        $this->current->appendChild($element);
        $this->current = $element;

        if (null !== $value) {
            $this->text($value);
        }

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function text($content)
    {
        $this->current->appendChild($this->dom->createTextNode($content));

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @param string $namespace
     *
     * @return $this
     */
    public function attr($name, $value, $namespace = null)
    {
        null !== $namespace ? $this->current->setAttributeNS($namespace, $name, $value) : $this->current->setAttribute($name, $value);

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function comment($content)
    {
        $this->current->appendChild($this->dom->createComment(htmlentities($content, ENT_QUOTES|ENT_XML1)));

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function cdata($content)
    {
        $this->current->appendChild($this->dom->createCDATASection($content));

        return $this;
    }

    /**
     * @param bool $formatOutput
     *
     * @return string
     */
    public function getXml($formatOutput = null)
    {
        if (is_bool($formatOutput)) {
            $domFormatOutput = $this->dom->formatOutput;
            $this->dom->formatOutput = $formatOutput;
        }

        $xml = $this->current instanceof \DOMElement
            ? $this->dom->saveXML($this->current)
            : $this->dom->saveXML()
        ;

        if (is_bool($formatOutput)) {
            $this->dom->formatOutput = $domFormatOutput;
        }

        return $xml;
    }

    /**
     * @param bool $formatOutput
     *
     * @return string
     */
    public function getInnerXml($formatOutput = null)
    {
        if (is_bool($formatOutput)) {
            $domFormatOutput = $this->dom->formatOutput;
            $this->dom->formatOutput = $formatOutput;
        }

        $xml = '';
        foreach ($this->current->childNodes as $node) {
            $xml .= $this->dom->saveXML($node);

            if ($this->dom->formatOutput) {
                $xml .= PHP_EOL;
            }
        }

        if (is_bool($formatOutput)) {
            $this->dom->formatOutput = $domFormatOutput;
        }

        return $xml;
    }

    /**
     * @param string $filename
     * @param bool $formatOutput
     *
     * @return bool
     */
    public function save($filename, $formatOutput = null)
    {
        if (is_bool($formatOutput)) {
            $domFormatOutput = $this->dom->formatOutput;
            $this->dom->formatOutput = $formatOutput;
        }

        $result = false !== $this->dom->save($filename);

        if (is_bool($formatOutput)) {
            $this->dom->formatOutput = $domFormatOutput;
        }

        return $result;
    }
}
