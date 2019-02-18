<?php

namespace Drupal\druki_parser\CommonMark\Inline\Parser;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

/**
 * Class OpenBracerParser
 *
 * @package Drupal\druki_parser\CommonMark\Inline\Parser
 */
class OpenBracerParser extends AbstractInlineParser {

  /**
   * {@inheritdoc}
   */
  public function getCharacters() {
    return ['{'];
  }

  /**
   * {@inheritdoc}
   */
  public function parse(InlineParserContext $inline_context) {
    $cursor = $inline_context->getCursor();

    // {{ opener.
    if ($cursor->getNextNonSpaceCharacter() == '{') {
      $cursor->advance();
      $node = new Text('{', ['delim' => true]);
      $inline_context->getContainer()->appendChild($node);

      $delimiter = new Delimiter('{', 1, $node, TRUE, FALSE, $cursor->getPosition());
      $inline_context->getDelimiterStack()->push($delimiter);

      return TRUE;
    }

    return FALSE;
  }

}
