<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

use Drupal\Component\Render\FormattableMarkup;

/**
 * Class ParagraphHeading.
 *
 * The value for paragraph heading type.
 *
 * @package Drupal\druki_content\Sync\ParsedContent\Content
 */
final class ParagraphHeading extends ParagraphContentBase {

  /**
   * The paragraph type.
   */
  protected string $paragraphType = 'druki_heading';

  /**
   * The available types of heading levels.
   *
   * @var array
   */
  private array $availableLevels = [
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
  ];

  /**
   * The heading level.
   */
  private string $level;

  /**
   * The heading content.
   */
  private string $content;

  /**
   * ParagraphNote constructor.
   *
   * @param string $level
   *   The heading level.
   * @param string $content
   *   The heading content.
   */
  public function __construct(string $level, string $content) {
    $this->setLevel($level);
    $this->setContent($content);
  }

  /**
   * Sets and validates level value.
   *
   * @param string $level
   *   The heading level.
   */
  private function setLevel(string $level): void {
    if (!\in_array($level, $this->availableLevels)) {
      $message = new FormattableMarkup('The heading level must be one of the following: @allowed_values. Got value: @value.', [
        '@allowed_values' => \implode(', ', $this->availableLevels),
        '@value' => $level,
      ]);
      throw new \InvalidArgumentException($message);
    }

    $this->level = $level;
  }

  /**
   * Sets and validates content value.
   *
   * @param string $content
   *   The heading content.
   */
  private function setContent(string $content): void {
    if (!\mb_strlen($content)) {
      throw new \InvalidArgumentException("The heading content can't be empty.");
    }

    $this->content = $content;
  }

  /**
   * Gets heading level.
   *
   * @return string
   *   The heading level.
   */
  public function getLevel(): string {
    return $this->level;
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
