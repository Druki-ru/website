<?php

namespace Drupal\druki_content\ParsedContent\Content;

use InvalidArgumentException;

/**
 * Class ParagraphCode.
 *
 * The value for paragraph code type.
 *
 * @package Drupal\druki_content\ParsedContent\Content
 */
final class ParagraphCode extends ParagraphContentBase {

  /**
   * The paragraph type.
   *
   * @var string
   */
  protected $paragraphType = 'druki_code';

  /**
   * The heading content.
   *
   * @var string
   */
  private $content;

  /**
   * ParagraphNote constructor.
   *
   * @param string $content
   *   The code content.
   */
  public function __construct(string $content) {
    $this->setContent($content);
  }

  /**
   * Validates and sets content value.
   *
   * @param string $content
   *   The paragraph content.
   */
  private function setContent(string $content): void {
    if (!mb_strlen($content)) {
      throw new InvalidArgumentException("The code content can't be empty.");
    }

    $this->content = $content;
  }

  /**
   * Gets heading content.
   *
   * @return string
   *   The heading content.
   */
  public function getContent(): string {
    return $this->content;
  }

}
