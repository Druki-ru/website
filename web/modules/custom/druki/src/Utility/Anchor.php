<?php

namespace Drupal\druki\Utility;

use Drupal\Component\Transliteration\PhpTransliteration;

/**
 * Class Text with simple string utility.
 */
class Anchor {

  /**
   * Indicated incremental anchors.
   *
   * @var int
   */
  public const COUNTER = 1;

  /**
   * Indicates reusable anchors.
   *
   * @var int
   */
  public const REUSE = 2;

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
   *   - COUNTER: Each new anchor will have suffix "-N".
   *   - REUSE: Will return already generated anchor.
   *
   * @return string
   *   The anchor string.
   */
  public static function generate(string $text, string $id = 'default', int $duplicate_mode = self::COUNTER): string {
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

    static $anchor_static = [];
    while (!$anchor_generated) {
      $key = "{$duplicate_mode}:{$id}:{$anchor}:{$iteration}";
      if (!isset($anchor_static[$key])) {
        if ($iteration > 0 && $duplicate_mode == self::COUNTER) {
          $anchor .= '-' . $iteration;
        }

        $anchor_static[$key] = $anchor;
        $anchor_generated = TRUE;
      }

      $iteration++;
    }

    return $anchor;
  }

}
