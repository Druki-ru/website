<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

/**
 * Provides interface for parsed paragraph.
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
