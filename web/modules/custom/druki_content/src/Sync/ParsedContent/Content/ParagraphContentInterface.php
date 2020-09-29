<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

/**
 * Interface ParagraphContentInterface.
 *
 * @package Drupal\druki_content\Sync\ParsedContent\Content
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
