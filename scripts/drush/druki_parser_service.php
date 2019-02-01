<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser');

$markdown_content = <<<'Markdown'
## Title

paragraph

```php
code
```

[url](https://google.ru)
Markdown;

$result_html = $druki_parser->parseMarkdown($markdown_content);

dump($druki_parser->parseHtml($result_html));