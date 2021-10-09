<?php

namespace Drupal\druki\Markdown\CommonMark\Block\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\AbstractStringContainerBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Util\RegexHelper;

/**
 * Provides Front Matter element.
 */
final class FrontMatterElement extends AbstractStringContainerBlock {

  /**
   * Indicates is meta information closing block found.
   */
  protected bool $isCloserFound;

  /**
   * FrontMatter constructor.
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
    return !$this->isCloserFound();
  }

  /**
   * Checks is element close markup is found.
   *
   * @return bool
   *   TRUE is found, FALSE otherwise.
   */
  public function isCloserFound(): bool {
    return $this->isCloserFound;
  }

  /**
   * Sets value is closer found or not.
   *
   * @param bool $status
   *   The status is closer found or not.
   */
  public function setIsCloserFound(bool $status): void {
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

    /** @var \League\CommonMark\Block\Element\AbstractStringContainerBlock $string_block */
    $string_block = $context->getTip();
    $string_block->addLine($cursor->getRemainder());
  }

  /**
   * {@inheritDoc}
   */
  public function finalize(ContextInterface $context, int $endLineNumber) {
    parent::finalize($context, $endLineNumber);

    $this->finalStringContents = \implode(\PHP_EOL, $this->strings->toArray());
  }

}
