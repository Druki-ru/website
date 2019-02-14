<?php

namespace Drupal\druki_parser\CommonMark\Block\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

/**
 * Class MetaInformationElement
 *
 * @package Drupal\druki_parser\Plugin\Markdown\Extension
 */
class MetaInformationElement extends AbstractBlock {

  /**
   * Indicates is meta information closing block found.
   *
   * @var bool
   */
  protected $isCloserFound;

  /**
   * MetaInformation constructor.
   */
  public function __construct() {
    parent::__construct();

    $this->isCloserFound = FALSE;
  }

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
    // @todo Avoid it. Not sure this is good approach, but only working with
    // matches. Try some workaround with $context->advanceBy(-3) in
    // handleRemainingContents(). This will let condition pass. But need some
    // extra fixes, since this not fix all the problems that fix this value.
    return TRUE;
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
    if ($this->isCloserFound()) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * @return bool
   */
  public function isCloserFound() {
    return $this->isCloserFound;
  }

  /**
   * @param bool $isCloserFound
   */
  public function setIsCloserFound($status) {
    $this->isCloserFound = $status;
  }

  /**
   * {@inheritdoc}
   */
  public function handleRemainingContents(ContextInterface $context, Cursor $cursor) {
    if ($cursor->getNextNonSpaceCharacter() == '-') {
      $match = RegexHelper::matchAll('/^\-{3}$/', $cursor->getLine(), $cursor->getNextNonSpacePosition());
      if (!empty($match)) {
        $this->setIsCloserFound(TRUE);
        return;
      }
    }

    $context->getTip()->addLine($cursor->getRemainder());
  }

}
