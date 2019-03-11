<?php

namespace Drupal\druki_parser\CommonMark\Block\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Cursor;

/**
 * Class NoteElement
 *
 * @package Drupal\druki_parser\Plugin\Markdown\Extension
 */
class NoteElement extends AbstractBlock {

  /**
   * {@inheritdoc}
   */
  public function canContain(AbstractBlock $block): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function acceptsLines(): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isCode(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldLastLineBeBlank(Cursor $cursor, $currentLineNumber): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function matchesNextLine(Cursor $cursor): bool {
    dump('TEST');
    dump($cursor->getNextNonSpaceCharacter());
    if (!$cursor->isIndented() && $cursor->getNextNonSpaceCharacter() === '>') {
      $cursor->advanceToNextNonSpaceOrTab();
      $cursor->advance();
      $cursor->advanceBySpaceOrTab();

      return TRUE;
    }

    return FALSE;
  }

}
