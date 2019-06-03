<?php

namespace Drupal\druki_paragraph\Common\ParagraphContent;

use Drupal\Component\Render\FormattableMarkup;
use UnexpectedValueException;

/**
 * Class ParagraphContentBase.
 *
 * The base object for all value objects used for paragraphs.
 *
 * @package Drupal\druki_paragraph\Common\ParagraphContent
 */
abstract class ParagraphContentBase implements ParagraphContentInterface {

  /**
   * The paragraph type.
   *
   * @var string
   */
  protected $paragraphType;

  /**
   * {@inheritDoc}
   */
  public function getParagraphType(): string {
    if (!$this->paragraphType) {
      $message = new FormattableMarkup('The paragraph content value objects must contain paragraph type. Consider to set this value in "$paragraphType" property for @class.', [
        '@class' => get_class($this),
      ]);
      throw new UnexpectedValueException($message);
    }

    return $this->paragraphType;
  }

}
