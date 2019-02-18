<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiMarkdownParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser.markdown');

$markdown_content = <<<'Markdown'
---meta
id: code-of-conduct
title: Нормы поведения
---

"quotes"

{faq}(FAQ)

Проверка внутренних ссылок внутри {test}(текста).

Проверка внутренних ссылок внутри {test}(текста), но сразу несколько {test}(ссылок).

## Title
Markdown;

$result_html = $druki_parser->parse($markdown_content);

dump($result_html);
