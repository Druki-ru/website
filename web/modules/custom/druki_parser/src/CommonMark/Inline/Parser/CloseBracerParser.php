<?php

namespace Drupal\druki_parser\CommonMark\Inline\Parser;

use Drupal\druki_parser\CommonMark\Inline\Element\InternalLinkElement;
use League\CommonMark\Cursor;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

/**
 * Class CloseBracerParser
 *
 * @package Drupal\druki_parser\CommonMark\Inline\Parser
 */
class CloseBracerParser extends AbstractInlineParser {

  /**
   * {@inheritdoc}
   */
  public function getCharacters() {
    return ['}'];
  }

  /**
   * {@inheritdoc}
   */
  public function parse(InlineParserContext $inline_context) {
    $cursor = $inline_context->getCursor();

    $opener = $inline_context->getDelimiterStack()->searchByCharacter('{');
    if ($opener == NULL) {
      return FALSE;
    }

    if (!$opener->isActive()) {
      $inline_context->getDelimiterStack()->removeDelimiter($opener);

      return FALSE;
    }

    $start_position = $cursor->getPosition();
    $cursor_previous_state = $cursor->saveState();

    $cursor->advance();

    $link_title = $this->parseInternalLink($cursor);
    if (!$link_title) {
      $cursor->restoreState($cursor_previous_state);

      return FALSE;
    }

    // @todo complete it.
    $internal_link = new InternalLinkElement('test', $link_title);
    $opener->getInlineNode()->replaceWith($internal_link);

    return TRUE;
  }

  protected function parseInternalLink(Cursor $cursor) {

    // Link must follow up with "(" for label.
    if ($cursor->getCharacter() !== '(') {
      return FALSE;
    }

    $cursor_previous_state = $cursor->saveState();

    $cursor->advance();
    $cursor->advanceToNextNonSpaceOrNewline();

    $title_close_found = FALSE;

    while (($character = $cursor->getCharacter()) !== NULL) {
      if ($character == ')') {
        $title_close_found = TRUE;
        break;
      }

      $cursor->advance();
    }

    $end_position = $cursor->getPosition();
    $cursor->restoreState($cursor_previous_state);

    $cursor->advanceBy($end_position - $cursor->getPosition());
    $link_title = $cursor->getPreviousText();

    return $title_close_found ? $link_title : FALSE;
  }

}
