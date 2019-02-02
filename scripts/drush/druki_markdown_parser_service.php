<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiMarkdownParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser.markdown');

$markdown_content = <<<'Markdown'
## Title

paragraph

leading paragraph

```php
code
```

![](https://google.ru/logo.png)

[url](https://google.ru)
Markdown;

$result_html = $druki_parser->parseMarkdown($markdown_content);

dump($druki_parser->parseHtml($result_html));