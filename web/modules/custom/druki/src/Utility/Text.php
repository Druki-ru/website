<?php

namespace Drupal\druki\Utility;

use Drupal\Component\Transliteration\PhpTransliteration;

/**
 * Class Text with simple string utility.
 *
 * @package Drupal\druki\Utility
 */
class Text {

  /**
   * Indicated incremental anchors.
   *
   * @var int
   */
  const ANCHOR_DUPLICATE_COUNTER = 1;

  /**
   * Indicates reusable anchors.
   *
   * @var int
   */
  const ANCHOR_DRUPLCATE_REUSE = 2;

  /**
   * Generates anchor for string.
   *
   * @param string $text
   *   The string to generate anchor from.
   * @param string $id
   *   The string ID for static caching during a single request. This helps
   *   generate unique anchors for the provided ID. When you generate anchors
   *   for entity headings, you expect anchor for "Title" will be "title" for
   *   each individual entity, but there is a change that one entity can contain
   *   two "Title" headings, so first will have anchor "title", second "title-1"
   *   and so on.
   * @param int $duplicate_mode
   *   The mode used when anchor for provided text and id is already exists.
   *   Available values:
   *   - ANCHOR_DUPLICATE_COUNTER: Each new anchor will have suffix "-N".
   *   - ANCHOR_DRUPLCATE_REUSE: Will return already generated anchor.
   *
   * @return string
   *   The anchor string.
   */
  public static function anchor(string $text, string $id = 'default', int $duplicate_mode = self::ANCHOR_DUPLICATE_COUNTER): string {
    $anchor_generated = FALSE;
    $iteration = 0;

    // Main processing for anchors.
    $transliteration = new PhpTransliteration();
    $anchor = $transliteration->transliterate($text);
    $anchor = strtolower($anchor);
    $anchor = trim($anchor);
    // Replace all spaces with dash.
    $anchor = preg_replace("/[\s_]/", '-', $anchor);
    // Remove everything else. Only alphabet, numbers and dash is allowed.
    $anchor = preg_replace("/[^0-9a-z-]/", '', $anchor);
    // Replace multiple dashes with single. F.e. "Title with - dash".
    $anchor = preg_replace('/-{2,}/', '-', $anchor);

    while (!$anchor_generated) {
      $anchor_static = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $id . ':' . $anchor . ':' . $iteration);

      // If anchor not stored in static cache, we generate new one.
      if (!isset($anchor_static)) {
        if ($iteration > 0 && $duplicate_mode == self::ANCHOR_DUPLICATE_COUNTER) {
          $anchor .= '-' . $iteration;
        }

        $anchor_static = $anchor;
        $anchor_generated = TRUE;
      }
      else {
        $iteration++;
      }
    }

    return $anchor;
  }

}
