<?php

namespace Drupal\druki_parser\CommonMark\Inline\Parser;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

/**
 * Class OpenBracerParser
 *
 * @package Drupal\druki_parser\CommonMark\Inline\Parser
 */
class OpenBracerParser implements InlineParserInterface {

  /**
   * {@inheritdoc}
   */
  public function getCharacters(): array {
    return ['{'];
  }

  /**
   * {@inheritdoc}
   */
  public function parse(InlineParserContext $inline_context): bool {
    $cursor = $inline_context->getCursor();

    // { opener.
    if ($cursor->getNextNonSpaceCharacter() == '{') {
      $cursor->advance();
      $node = new Text('{', ['delim' => TRUE]);
      $inline_context->getContainer()->appendChild($node);

      $delimiter = new Delimiter('{', 1, $node, TRUE, FALSE, $cursor->getPosition());
      $inline_context->getDelimiterStack()->push($delimiter);

      return TRUE;
    }

    return FALSE;
  }

}
