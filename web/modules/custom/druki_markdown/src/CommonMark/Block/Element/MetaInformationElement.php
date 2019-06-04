<?php

namespace Drupal\druki_markdown\CommonMark\Block\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\AbstractStringContainerBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

/**
 * Class MetaInformationElement
 *
 * @package Drupal\druki_markdown\Plugin\Markdown\Extension
 */
class MetaInformationElement extends AbstractStringContainerBlock {

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
    // @see https://github.com/thephpleague/commonmark/issues/358#issuecomment-485248825
    return TRUE;
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
    if ($this->isCloserFound()) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * @return bool
   */
  public function isCloserFound(): bool {
    return $this->isCloserFound;
  }

  /**
   * @param bool $isCloserFound
   */
  public function setIsCloserFound($status): void {
    $this->isCloserFound = $status;
  }

  /**
   * {@inheritdoc}
   */
  public function handleRemainingContents(ContextInterface $context, Cursor $cursor): void {
    if ($cursor->getNextNonSpaceCharacter() == '-') {
      $match = RegexHelper::matchAll('/^\-{3}$/', $cursor->getLine(), $cursor->getNextNonSpacePosition());
      if (!empty($match)) {
        $this->setIsCloserFound(TRUE);
        return;
      }
    }

    $context->getTip()->addLine($cursor->getRemainder());
  }

  /**
   * {@inheritDoc}
   */
  public function finalize(ContextInterface $context, int $endLineNumber) {
    parent::finalize($context, $endLineNumber);

    $this->finalStringContents = implode(PHP_EOL, $this->strings->toArray());
  }

}
