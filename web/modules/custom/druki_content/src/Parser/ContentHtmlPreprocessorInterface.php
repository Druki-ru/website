<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentParserContext;

/**
 * Provides interface for content html parser preprocessor.
 *
 * Preprocessor should process HTML string before it will be passed to parser.
 * This is the only way to access and modify whole HTML before it will be parsed
 * into structured content.
 */
interface ContentHtmlPreprocessorInterface {

  /**
   * Preprocess HTML before it passed for parsing.
   *
   * @param string $html
   *   The HTML content.
   * @param \Drupal\druki_content\Data\ContentParserContext $context
   *   The parser context.
   *
   * @return string
   *   The preprocessed HTML.
   */
  public function preprocess(string $html, ContentParserContext $context): string;

}
