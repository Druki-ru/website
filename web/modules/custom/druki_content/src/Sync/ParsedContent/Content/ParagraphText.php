<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

/**
 * Class ParagraphText.
 *
 * The value for paragraph text type.
 *
 * @package Drupal\druki_content\Sync\ParsedContent\Content
 */
final class ParagraphText extends ParagraphContentBase {

  /**
   * The paragraph type.
   */
  protected string $paragraphType = 'druki_text';

  /**
   * The text content.
   */
  private string $content;

  /**
   * ParagraphNote constructor.
   *
   * @param string $content
   *   The text content.
   */
  public function __construct(string $content) {
    $this->setContent($content);
  }

  /**
   * Sets and validates content.
   *
   * @param string $content
   *   The content value.
   */
  private function setContent(string $content): void {
    if (!\mb_strlen($content)) {
      throw new \InvalidArgumentException("The text content can't be empty.");
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
