<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\Content;

/**
 * Provides interface for content HTML parser.
 *
 * @todo Add parsers, the text parser should be with -1000 priority and works
 *   as fallback.
 */
interface ContentHtmlElementParserInterface {

  /**
   * Parses content from an element.
   *
   * @return bool
   *   TRUE if successfully processed current element, FALSE to let other
   *   parser to process it.
   */
  public function parse(\DOMElement $element, Content $content): bool;

}
