Easy PHP XML Builder
====================

Generate XML
------------
```php
<?php

$xmlBuilder = new XmlBuilder();

$xmlBuilder
  ->element('root')
    ->element('ns:element', 'value', 'http://ns/ns')
      ->attr('ns:attr1', 'value1', 'http://ns/ns')
      ->attr('attr2', 'value2')
    ->end()
  ->end()
;

echo $xmlBuilder->getXml(true);
```

Result:
```xml
<?xml version="1.0" encoding="utf-8"?>
<root>
  <ns:element xmlns:ns="http://ns/ns" ns:attr1="value1" attr2="value2">value</ns:element>
</root>
```

Reference
---------
```php
<?php

$xmlBuilder = new XmlBuilder();

$xmlBuilder
  ->element('root')
    ->element('user')
      ->element('addresses')->reference($addresses)->end()
    ->end()
  ->end()
;

$addresses->element('address', 'value1')->end();
$addresses->element('address', 'value2')->end();

echo $xmlBuilder->getXml(true);
```

Result:
```xml
<?xml version="1.0" encoding="utf-8"?>
<root>
  <user>
    <addresses>
      <address>value1</address>
      <address>value2</address>
    </addresses>
  </user>
</root>
```
