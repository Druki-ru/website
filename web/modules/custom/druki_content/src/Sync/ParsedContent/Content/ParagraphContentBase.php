<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

use Drupal\Component\Render\FormattableMarkup;

/**
 * Class ParagraphContentBase.
 *
 * The base object for all value objects used for paragraphs.
 *
 * @package Drupal\druki_content\Sync\ParsedContent\Content
 */
abstract class ParagraphContentBase implements ParagraphContentInterface {

  /**
   * The paragraph type.
   */
  protected string $paragraphType;

  /**
   * {@inheritDoc}
   */
  public function getParagraphType(): string {
    if (!$this->paragraphType) {
      $message = new FormattableMarkup('The paragraph content value objects must contain paragraph type. Consider to set this value in "$paragraphType" property for @class.', [
        '@class' => static::class,
      ]);
      throw new \UnexpectedValueException($message);
    }

    return $this->paragraphType;
  }

}
