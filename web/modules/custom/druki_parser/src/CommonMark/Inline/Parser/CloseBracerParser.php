<?php

namespace Drupal\druki_parser\CommonMark\Inline\Parser;

use Drupal\druki_parser\CommonMark\Inline\Element\InternalLinkElement;
use League\CommonMark\Cursor;
use League\CommonMark\Environment;
use League\CommonMark\EnvironmentAwareInterface;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

/**
 * Class CloseBracerParser
 *
 * @package Drupal\druki_parser\CommonMark\Inline\Parser
 */
class CloseBracerParser extends AbstractInlineParser implements EnvironmentAwareInterface {

  /**
   * @var Environment
   */
  protected $environment;

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
    $opener = $inline_context->getDelimiterStack()->searchByCharacter('{');
    if ($opener == NULL) {
      return FALSE;
    }

    if (!$opener->isActive()) {
      $inline_context->getDelimiterStack()->removeDelimiter($opener);

      return FALSE;
    }

    $cursor = $inline_context->getCursor();

    $cursor_previous_state = $cursor->saveState();

    $content_id = $this->parseInternalLink($cursor);
    if (!$content_id) {
      $inline_context->getDelimiterStack()->removeDelimiter($opener);
      $cursor->restoreState($cursor_previous_state);

      return FALSE;
    }

    // Creates our internal link node element.
    $internal_link = new InternalLinkElement($content_id);
    // Replace opener "{" with it.
    $opener->getInlineNode()->replaceWith($internal_link);

    // Loop through next nodes of our element and append them inside ours.
    while (($label = $internal_link->next()) !== NULL) {
      $internal_link->appendChild($label);
    }

    return TRUE;
  }

  /**
   * Parses internal link.
   *
   * @param \League\CommonMark\Cursor $cursor
   *   The current cursor state.
   *
   * @return bool|string
   *   The content id for internal link element, FALSE otherwise.
   */
  protected function parseInternalLink(Cursor $cursor) {
    // Link must follow up with "(" for URL.
    if ($cursor->peek() !== '(') {
      return FALSE;
    }

    $cursor_previous_state = $cursor->saveState();

    $cursor->advance();
    $cursor->advanceToNextNonSpaceOrNewline();

    while (($character = $cursor->getCharacter()) !== NULL) {
      if ($character == ')') {
        break;
      }

      $cursor->advance();
    }

    $end_position = $cursor->getPosition();
    $cursor->restoreState($cursor_previous_state);

    // To skip "}(".
    $cursor->advanceBy(2);
    $cursor->advanceBy($end_position - $cursor->getPosition());
    $content_id = $cursor->getPreviousText();

    $cursor->advanceToNextNonSpaceOrNewline();

    if ($cursor->match('/^\\)/') === NULL) {
      $cursor->restoreState($cursor_previous_state);

      return FALSE;
    }

    return $content_id;
  }

  /**
   * @param Environment $environment
   *
   * @return void
   */
  public function setEnvironment(Environment $environment) {
    $this->environment = $environment;
  }

}
