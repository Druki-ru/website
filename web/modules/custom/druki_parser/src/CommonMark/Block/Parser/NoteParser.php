<?php

namespace Drupal\druki_parser\CommonMark\Block\Parser;

use Drupal\druki_parser\CommonMark\Block\Element\NoteElement;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * Class NoteParser
 *
 * @package Drupal\druki_parser\CommonMark\Block\Parser
 */
class NoteParser implements BlockParserInterface {

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

    $state = $cursor->saveState();

    $cursor->advanceToNextNonSpaceOrTab();
    $cursor->advance();
    $cursor->advanceBySpaceOrTab();

    $matched = $cursor->match("/\[\!(NOTE|WARNING|TIP|IMPORTANT)\]/");
    if (!$matched) {
      $cursor->restoreState($state);

      return FALSE;
    }

    preg_match("/\[\!(.+)\]/", $matched, $matches);
    $note_type = mb_strtolower($matches[1]);

    $context->addBlock(new NoteElement($note_type));

    return TRUE;
  }

}
