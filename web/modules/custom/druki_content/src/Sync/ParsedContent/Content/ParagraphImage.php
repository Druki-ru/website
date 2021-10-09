<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

/**
 * Class ParagraphImage.
 *
 * The value for paragraph image type.
 *
 * @package Drupal\druki_content\Common\ParagraphContent
 */
final class ParagraphImage extends ParagraphContentBase {

  /**
   * The paragraph type.
   */
  protected string $paragraphType = 'druki_image';

  /**
   * The image source uri.
   */
  private string $src;

  /**
   * The image alt.
   */
  private string $alt;

  /**
   * ParagraphNote constructor.
   *
   * @param string $src
   *   The image src.
   * @param string|null $alt
   *   The image alt.
   */
  public function __construct(string $src, ?string $alt = NULL) {
    $this->src = $src;
    $this->alt = $alt;
  }

  /**
   * Gets image src.
   *
   * @return string
   *   The image src.
   */
  public function getSrc(): string {
    return $this->src;
  }

  /**
   * Gets image alt.
   *
   * @return string
   *   The alt value.
   */
  public function getAlt(): ?string {
    return $this->alt;
  }

}
