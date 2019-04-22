<?php

namespace Drupal\druki_paragraphs\Content;

use Drupal\Component\Render\FormattableMarkup;
use InvalidArgumentException;

/**
 * Class ParagraphImage.
 *
 * The value for paragraph image type.
 *
 * @package Drupal\druki_paragraphs\Content
 */
final class ParagraphImage extends ParagraphContentBase {

  /**
   * The paragraph type.
   *
   * @var string
   */
  protected $paragraphType = 'druki_image';

  /**
   * The image source uri.
   *
   * @var string
   */
  protected $src;

  /**
   * The image alt.
   *
   * @var string
   */
  protected $alt;

  /**
   * ParagraphNote constructor.
   *
   * @param string $src
   *   The image src.
   * @param string|null $alt
   *   The image alt.
   */
  public function __construct(string $src, string $alt = NULL) {
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
