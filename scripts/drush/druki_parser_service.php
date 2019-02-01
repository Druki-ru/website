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
Markdown;

/** @var \Drupal\markdown\MarkdownInterface $markdown */
$markdown = \Drupal::service('markdown');
$commonmark = $markdown->getParser('thephpleague/commonmark');
dump($commonmark->parse($markdown_content));
