<?php

class XmlElement
{
    /**
     * @var \DOMElement
     */
    protected $current;

    /**
     * @var \DomDocument
     */
    protected $dom;

    /**
     * @param DOMElement $element
     */
    public function __construct(\DOMElement $element)
    {
        if (null === $element->ownerDocument) {
            throw new \LogicException('Owner document is not set');
        }

        $this->current = $element;
        $this->dom = $element->ownerDocument;
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
        $name = new self($this->current);

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
        $element = $namespace ? $this->dom->createElementNS($namespace, $name) : $this->dom->createElement($name);

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
        $namespace ? $this->current->setAttributeNS($namespace, $name, $value) : $this->current->setAttribute($name, $value);

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function comment($content)
    {
        $this->current->appendChild($this->dom->createComment($content));

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
}
