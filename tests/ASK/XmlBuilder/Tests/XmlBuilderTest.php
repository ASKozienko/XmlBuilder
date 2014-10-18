<?php
namespace ASK\XmlBuilder\Tests;

use ASK\XmlBuilder\XmlBuilder;

class XmlBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateXmlDeclaration()
    {
        $xmlBuilder = new XmlBuilder();

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldCreateXmlElement()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder->element('root');

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root/>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldCreateChildXmlElement()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('root')
                ->element('child')->end()
            ->end()
        ;

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root>
  <child/>
</root>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldCreateChildXmlElementWithValue()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('root')
                ->element('child', 'value')->end()
            ->end()
        ;

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root>
  <child>value</child>
</root>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldCreateXmlElementWithAttributes()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('root')
                ->attr('attr1', 'value1')
                ->attr('attr2', 'value2')
            ->end()
        ;

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root attr1="value1" attr2="value2"/>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldCreateXmlElementWithNamespacedAttribute()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('root')
                ->attr('ns:attr', 'value', 'http://ns/ns')
            ->end()
        ;

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root xmlns:ns="http://ns/ns" ns:attr="value"/>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldCreateXmlElementWithNamespace()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('ns:root', null, 'http://ns/ns')->end()
        ;

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<ns:root xmlns:ns="http://ns/ns"/>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldEscapeSpecialChars()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('root')
                ->element('element',  '&<')->end()
                ->element('text')->text('&<')->end()
                ->element('attr')->attr('attr', '&<')->end()
                ->comment('&<')
            ->end()
        ;

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root>
  <element>&amp;&lt;</element>
  <text>&amp;&lt;</text>
  <attr attr="&amp;&lt;"/>
  <!--&amp;&lt;-->
</root>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldNotEscapeSpecialCharsInCdata()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('root')
                ->element('cdata')->cdata('<&>')->end()
            ->end()
        ;

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root>
  <cdata><![CDATA[<&>]]></cdata>
</root>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }

    public function testShouldReturnReferenceToElement()
    {
        $xmlBuilder = new XmlBuilder();
        $xmlBuilder
            ->element('root')
                ->element('child1')->reference($child1)
            ->end()
        ;

        $child1->element('child2');

        $expected = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<root>
  <child1>
    <child2/>
  </child1>
</root>

EOF;

        $this->assertEquals($expected, $xmlBuilder->getXml(true));
    }
}
