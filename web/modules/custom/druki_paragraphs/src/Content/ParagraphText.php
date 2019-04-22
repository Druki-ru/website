<?php

namespace Drupal\druki_paragraphs\Content;

use Drupal\Component\Render\FormattableMarkup;
use InvalidArgumentException;

/**
 * Class ParagraphText.
 *
 * The value for paragraph text type.
 *
 * @package Drupal\druki_paragraphs\Content
 */
final class ParagraphText extends ParagraphContentBase {

  /**
   * The paragraph type.
   *
   * @var string
   */
  protected $paragraphType = 'druki_text';

  /**
   * The text content.
   *
   * @var string
   */
  protected $content;

  /**
   * ParagraphNote constructor.
   *
   * @param string $content
   *   The text content.
   */
  public function __construct(string $content) {
    if (!mb_strlen($content)) {
      throw new InvalidArgumentException("The text content can't be empty.");
    }

    $this->content = $content;
  }

  /**
   * Gets text content.
   *
   * @return string
   *   The text content.
   */
  public function getContent(): string {
    return $this->content;
  }

}
