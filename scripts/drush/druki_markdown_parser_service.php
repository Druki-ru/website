<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiMarkdownParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser.markdown');

$markdown_content = <<<'Markdown'
---
id: installation
title: Установка Drupal 8
core: 8
category-area: Первое знакомство
category-order: 0
test-comma: one, two, three
test-array:
  - one
  - two
---

---

"quotes"

{**FAQ**}(faq)

Проверка внутренних ссылок внутри {текста}(test).

Проверка внутренних ссылок внутри {текста}(test), но сразу несколько {ссылок}(test).

## Title

> [!NOTE]
>
> This is a NOTE.
>
> But multiline content with **some** [markdown](https://commonmark.org) sauce.

> [!WARNING]
> This is a WARNING

> [!TIP]
>
> This is a TIP

> [!IMPORTANT]
> This is IMPORTANT

> Original Markdown Quote

* List
  * List
     * List
  * List
* List

1. List
1. List
   1. List
   1. List
1. List

{Drupal 8}[drupal:8]
Markdown;

$result_html = $druki_parser->parse($markdown_content);

dump($result_html);
