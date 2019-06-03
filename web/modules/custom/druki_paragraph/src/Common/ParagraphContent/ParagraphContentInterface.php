<?php

namespace Drupal\druki_paragraph\Common\ParagraphContent;

/**
 * Interface ParagraphContentInterface.
 *
 * @package Drupal\druki_paragraph\Common\ParagraphContent
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
