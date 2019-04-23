<?php

namespace Drupal\druki_paragraphs\Common\ParagraphContent;

use Drupal\Component\Render\FormattableMarkup;
use InvalidArgumentException;

/**
 * Class ParagraphNote.
 *
 * The value for paragraph note type.
 *
 * @package Drupal\druki_paragraphs\Common\ParagraphContent
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
  private $availableTypes = [
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
  private $type;

  /**
   * The note content.
   *
   * @var string
   */
  private $content;

  /**
   * ParagraphNote constructor.
   *
   * @param string $type
   *   The note type.
   * @param string $content
   *   The note value.
   */
  public function __construct(string $type, string $content) {
    $this->setType($type);
    $this->setContent($content);
  }

  /**
   * Sets and validates type value.
   *
   * @param string $type
   *   The note type.
   */
  private function setType(string $type): void {
    if (!in_array($type, $this->availableTypes)) {
      $message = new FormattableMarkup('The note type must be on of the following: @allowed_values. Got value: @value.', [
        '@allowed_values' => implode(', ', $this->availableTypes),
        '@value' => $type,
      ]);
      throw new InvalidArgumentException($message);
    }

    $this->type = $type;
  }

  /**
   * Sets and validates content value.
   *
   * @param string $content
   *   The note content.
   */
  private function setContent(string $content): void {
    if (!mb_strlen($content)) {
      throw new InvalidArgumentException("The note content can't be empty.");
    }

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
