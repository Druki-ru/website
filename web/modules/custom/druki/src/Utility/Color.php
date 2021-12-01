<?php

namespace Drupal\druki\Utility;

/**
 * Provides an object with color utilities.
 */
final class Color {

  /**
   * Converts text to color.
   *
   * @param string $text
   *   The text to convert.
   * @param int $saturation
   *   The color saturation.
   * @param int $lightness
   *   The color lightness.
   *
   * @return array
   *   The array with 'h', 's', 'l' values and 'processed' ready to inline.
   */
  public static function textToColor(string $text, int $saturation = 50, int $lightness = 50): array {
    $hash = 260;
    foreach (\str_split($text) as $char) {
      $hash = \ord($char) + ($hash << 3) - $hash;
    }
    $hue = \abs($hash) % 360;

    return [
      'h' => $hue,
      's' => $saturation,
      'l' => $lightness,
      'processed' => "hsl({$hue}, {$saturation}%, {$lightness}%)",
    ];
  }

}
