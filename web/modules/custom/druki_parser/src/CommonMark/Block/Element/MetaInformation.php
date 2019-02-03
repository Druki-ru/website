<?php

namespace Drupal\druki_parser\CommonMark\Block\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * Class MetaInformationElement
 *
 * @package Drupal\druki_parser\Plugin\Markdown\Extension
 */
class MetaInformation extends AbstractBlock {

  /**
   * {@inheritdoc}
   */
  public function canContain(AbstractBlock $block) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function acceptsLines() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isCode() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldLastLineBeBlank(Cursor $cursor, $currentLineNumber) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function matchesNextLine(Cursor $cursor) {
    if ($cursor->isBlank()) {
      return FALSE;
    }

    if ($cursor->getIndent()) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function handleRemainingContents(ContextInterface $context, Cursor $cursor) {
    if (preg_match("/^!!!$/", $context->getLine())) {
      // @todo.
    }

    $context->getTip()->addLine($cursor->getRemainder());
  }

}
