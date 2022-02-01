<?php

declare(strict_types=1);

namespace Drupal\druki\Markdown\CommonMark\Block\Element;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Cursor;

/**
 * Provides element for <Aside> syntax.
 */
final class AsideElement extends AbstractBlock {

  /**
   * An aside type.
   */
  protected string $type;

  /**
   * An aside header.
   */
  protected ?string $header;

  /**
   * Constructs a new AsideElement object.
   *
   * @param string $type
   *   An aside type.
   * @param string|null $header
   *   An aside header.
   */
  public function __construct(string $type, ?string $header = NULL) {
    $this->type = $type;
    $this->header = $header;
  }

  /**
   * Gets an aside element type.
   *
   * @return string
   *   An aside type.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Gets an aside element header.
   *
   * @return string|null
   *   A header value.
   */
  public function getHeader(): ?string {
    return $this->header;
  }

  /**
   * {@inheritdoc}
   */
  public function canContain(AbstractBlock $block): bool {
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
  public function matchesNextLine(Cursor $cursor): bool {
    return !$cursor->match('/<\/Aside>/');
  }

}
