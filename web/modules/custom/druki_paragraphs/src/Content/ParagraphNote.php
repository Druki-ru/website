<?php

namespace Drupal\druki_paragraphs\Content;

use Drupal\Component\Render\FormattableMarkup;
use InvalidArgumentException;

/**
 * Class ParagraphNote.
 *
 * The value for paragraph note type.
 *
 * @package Drupal\druki_paragraphs\Content
 */
final class ParagraphNote extends ParagraphContentBase {

  /**
   * The paragraph type.
   *
   * @var string
   */
  protected $paragraphType = 'druki_note';

  /**
   * The available types of notes supported by paragraph.
   *
   * @var array
   */
  protected $availableTypes = [
    'note',
    'warning',
    'tip',
    'important',
  ];

  /**
   * The note type.
   *
   * @var string
   */
  protected $type;

  /**
   * The note content.
   *
   * @var string
   */
  protected $content;

  /**
   * ParagraphNote constructor.
   *
   * @param string $type
   *   The note type.
   * @param string $content
   *   The note value.
   */
  public function __construct(string $type, string $content) {
    if (!in_array($type, $this->availableTypes)) {
      $message = new FormattableMarkup('The note type must be on of the following: @allowed_values. Got value: @value.', [
        '@allowed_values' => implode(', ', $this->availableTypes),
        '@value' => $type,
      ]);
      throw new InvalidArgumentException($message);
    }

    if (!mb_strlen($content)) {
      throw new InvalidArgumentException("The note content can't be empty.");
    }

    $this->type = $type;
    $this->content = $content;
  }

  /**
   * Gets note type.
   *
   * @return string
   *   The note type.
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * Gets note content.
   *
   * @return string
   *   The note content.
   */
  public function getContent(): string {
    return $this->content;
  }

}
