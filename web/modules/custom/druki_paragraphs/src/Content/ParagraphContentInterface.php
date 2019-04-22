<?php

namespace Drupal\druki_paragraphs\Content;

/**
 * Interface ParagraphContentInterface.
 *
 * @package Drupal\druki_paragraphs\Content
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
