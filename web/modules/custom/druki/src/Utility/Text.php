<?php

namespace Drupal\druki\Utility;

use Drupal;

/**
 * Class Text with simple string utility.
 *
 * @package Drupal\druki\Utility
 */
class Text {

  /**
   * Generates anchor for string.
   *
   * @param string $id
   *   The string ID for static caching during a single request. This helps
   *   generate unique anchors for the provided ID. When you generate anchors
   *   for entity headings, you expect anchor for "Title" will be "title" for
   *   each individual entity, but there is a change that one entity can contain
   *   two "Title" headings, so first will have anchor "title", second "title-1"
   *   and so on.
   * @param string $text
   *   The string to generate anchor from.
   *
   * @return string
   *   The anchor string.
   */
  public static function anchor(string $text, string $id = 'default'): string {
    $anchor_generated = FALSE;
    $iteration = 0;

    // Main processing for anchors.
    $anchor = Drupal::transliteration()->transliterate($text);
    $anchor = strtolower($anchor);
    $anchor = trim($anchor);
    // Replace all spaces with dash.
    $anchor = preg_replace("/[\s_]/", '-', $anchor);
    // Remove everything else. Only alphabet, numbers and dash is allowed.
    $anchor = preg_replace("/[^0-9a-z-]/", '', $anchor);
    // Replace multiple dashes with single. F.e. "Title with - dash".
    $anchor = preg_replace('/\-{2,}/', '-', $anchor);

    while (!$anchor_generated) {
      $anchor_static = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $id . ':' . $anchor . ':' . $iteration);

      // If anchor not stored in static cache, we generate new one.
      if (!isset($anchor_static)) {
        if ($iteration > 0) {
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
