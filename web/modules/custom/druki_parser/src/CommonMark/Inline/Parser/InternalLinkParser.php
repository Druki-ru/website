<?php

namespace Drupal\druki_parser\CommonMark\Inline\Parser;

use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

/**
 * Class InternalLinksParser
 *
 * @package Drupal\druki_parser\CommonMark\Inline\Parser
 */
class InternalLinkParser extends AbstractInlineParser {

  /**
   * {@inheritdoc}
   */
  public function getCharacters() {
    return ['['];
  }

  /**
   * {@inheritdoc}
   */
  public function parse(InlineParserContext $inlineContext) {
    dump('test');
  }

}
