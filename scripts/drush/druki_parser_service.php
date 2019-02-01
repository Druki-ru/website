<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser');

$markdown_content = <<<'Markdown'
---
id: meta-test
title: Meta info test
---

## h2

```php
<?php

echo "Hello World";
```

{-- removed content --}
{~~removed content~>ins~~}
{++ added content ++}
[Alt]+[Q]
Markdown;

dump($druki_parser->parseMarkdown($markdown_content));
