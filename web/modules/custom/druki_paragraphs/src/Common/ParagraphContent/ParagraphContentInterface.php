<?php

namespace Drupal\druki_paragraphs\Common\ParagraphContent;

/**
 * Interface ParagraphContentInterface.
 *
 * @package Drupal\druki_paragraphs\Common\ParagraphContent
 */
interface ParagraphContentInterface {

  /**
   * Gets paragraph type.
   *
   * @return string
   *   The paragraph type.
   */
  public function getParagraphType(): string;

}
