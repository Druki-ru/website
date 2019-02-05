<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiMarkdownParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser.markdown');

$markdown_content = <<<'Markdown'
...
id: code-of-conduct
title: Нормы поведения
...

## Title

```php
echo "Hello World";
```

![Image](/logo.svg)

"quotes"

Another text.
Markdown;

$result_html = $druki_parser->parse($markdown_content);

dump($result_html);
/** @var \Drupal\druki_parser\Service\DrukiHTMLParserInterface $druki_html_parser */
$druki_html_parser = \Drupal::service('druki_parser.html');
dump($druki_html_parser->parse($result_html));