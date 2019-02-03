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
  1 2 23  ...

```
code
code
```

## Title

paragraph

leading paragraph

@test

![](https://google.ru/logo.png)

[url](https://google.ru)
Markdown;

$result_html = $druki_parser->parse($markdown_content);

dump($result_html);