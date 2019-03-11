<?php

/**
 * @file
 * Testing code for druki parser service.
 */

/** @var \Drupal\druki_parser\Service\DrukiMarkdownParser $druki_parser */
$druki_parser = \Drupal::service('druki_parser.markdown');

$markdown_content = <<<'Markdown'
---
id: code-of-conduct
title: Нормы поведения
---

---

"quotes"

{**FAQ**}(faq)

Проверка внутренних ссылок внутри {текста}(test).

Проверка внутренних ссылок внутри {текста}(test), но сразу несколько {ссылок}(test).

## Title

> [!NOTE]
> Заметка

> [!WARNING]
> Предупреждение

> [!TIP]
> Совет

> [!IMPORTANT]
> Важная информация

> Original Markdown Quote
Markdown;

$result_html = $druki_parser->parse($markdown_content);

dump($result_html);
