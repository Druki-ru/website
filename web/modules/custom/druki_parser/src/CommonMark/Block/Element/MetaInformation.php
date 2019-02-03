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
class MetaInformation extends AbstractBlock {

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
    if ($this->isCloserFound()) {
      return FALSE;
    }

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
    //dump($cursor->getNextNonSpaceCharacter());
    if ($cursor->getNextNonSpaceCharacter() == '.') {
      $match = RegexHelper::matchAll('/^\.{3,}$/', $cursor->getLine(), $cursor->getNextNonSpacePosition());
      dump($match, 'match');
      $this->setIsCloserFound(TRUE);
    }

    $context->getTip()->addLine($cursor->getRemainder());
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

}
