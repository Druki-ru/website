<?php

namespace Drupal\druki_paragraphs\Content;

use Drupal\Component\Render\FormattableMarkup;
use InvalidArgumentException;

/**
 * Class ParagraphHeading.
 *
 * The value for paragraph heading type.
 *
 * @package Drupal\druki_paragraphs\Content
 */
final class ParagraphHeading extends ParagraphContentBase {

  /**
   * The paragraph type.
   *
   * @var string
   */
  protected $paragraphType = 'druki_heading';

  /**
   * The available types of heading levels.
   *
   * @var array
   */
  protected $availableLevels = [
    'h1',
    'h2',
    'h3',
    'h4',
    'h5',
    'h6',
  ];

  /**
   * The heading level.
   *
   * @var string
   */
  protected $level;

  /**
   * The heading content.
   *
   * @var string
   */
  protected $content;

  /**
   * ParagraphNote constructor.
   *
   * @param string $level
   *   The heading level.
   * @param string $content
   *   The heading content.
   */
  public function __construct(string $level, string $content) {
    if (!in_array($level, $this->availableLevels)) {
      $message = new FormattableMarkup('The note type must be on of the following: @allowed_values. Got value: @value.', [
        '@allowed_values' => implode(', ', $this->availableTypes),
        '@value' => $level,
      ]);
      throw new InvalidArgumentException($message);
    }

    if (!mb_strlen($content)) {
      throw new InvalidArgumentException("The heading content can't be empty.");
    }

    $this->level = $level;
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
