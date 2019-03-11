<?php

namespace Drupal\druki_parser\CommonMark\Block\Parser;

use League\CommonMark\Block\Element\BlockQuote;
use League\CommonMark\Block\Parser\AbstractBlockParser;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * Class NoteParser
 *
 * @package Drupal\druki_parser\CommonMark\Block\Parser
 */
class NoteParser extends AbstractBlockParser {

  /**
   * {@inheritdoc}
   */
  public function parse(ContextInterface $context, Cursor $cursor): bool {
    if ($cursor->isIndented()) {
      return FALSE;
    }

    if ($cursor->getNextNonSpaceCharacter() !== '>') {
      return FALSE;
    }

    $cursor->advanceToNextNonSpaceOrTab();
    $cursor->advance();
    $cursor->advanceBySpaceOrTab();

    $matched = $cursor->match("/\[\!(NOTE|WARNING|TIP|IMPORTANT)\]/");
    if (!$matched) {
      return FALSE;
    }

    // @todo custom element.
    $context->addBlock(new BlockQuote());

    return TRUE;
  }

}
