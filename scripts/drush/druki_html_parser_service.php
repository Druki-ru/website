<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\MarkdownParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser.markdown');

$markdown_content = <<<'Markdown'
---
id: installation
title: Установка Drupal 8
core: 8
category: 
  area: Первое знакомство
  order: 0
test-comma: ['one', 'two', 'three']
test-array:
  - one
  - two
metatags:
  title: 'Проверка заголовка'
  description: 'Проверка описания'
---

## Title

```php
echo "Hello World";
```

![Image](/logo.svg)

"quotes"

Another text.

> [!WARNING]
> **Requires** some [module](fake-link) to be installed.

[Test internal link to file](changelog.md)
Markdown;

$result_html = $druki_parser->parse($markdown_content);

//dump($result_html);
/** @var \Drupal\druki_parser\Service\DrukiHTMLParserInterface $druki_html_parser */
$druki_html_parser = \Drupal::service('druki_parser.html');
$druki_html_parser->parse($result_html, 'public://test/file.md');
