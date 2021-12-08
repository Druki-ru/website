<?php

namespace Drupal\druki\Utility;

use Drupal\Component\Utility\Color as CoreColor;

/**
 * Provides an object with color utilities.
 */
final class Color extends CoreColor {

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
  public static function textToHsl(string $text, int $saturation = 50, int $lightness = 50): array {
    $hash = 260;
    foreach (\str_split($text) as $char) {
      $hash = \ord($char) + ($hash << 3) - $hash;
    }
    $hue = \abs($hash) % 360;

    return [
      'h' => $hue,
      's' => $saturation,
      'l' => $lightness,
    ];
  }

  /**
   * Calculates contrast for a given color.
   *
   * @param string $color
   *   The HEX color.
   * @param string $light_result
   *   The result for light color.
   * @param string $dark_result
   *   The result for dark color.
   *
   * @return string
   *   The resulted color.
   */
  public static function colorContrast(string $color, string $light_result = 'white', string $dark_result = 'black'): string {
    if (!self::validateHex($color)) {
      throw new \InvalidArgumentException('Color must be in HEX format.');
    }
    $color_rgb = self::hexToRgb($color);
    $yiq = ($color_rgb['red'] * 299 + $color_rgb['green'] * 587 + $color_rgb['blue'] * 114) / 1000;
    return $yiq >= 128 ? $dark_result : $light_result;
  }

  /**
   * Converts HEX color to HSL.
   *
   * @param string $hex
   *   The HEX color.
   *
   * @return array
   *   An array with 'hue', 'saturation' and 'ligthness'.
   *
   * @see https://stackoverflow.com/a/13887939/4751623
   */
  public static function hexToHsl(string $hex): array {
    $rgb = CoreColor::hexToRgb($hex);
    $red = $rgb['red'] / 255;
    $green = $rgb['green'] / 255;
    $blue = $rgb['blue'] / 255;

    $min = \min($red, $green, $blue);
    $max = \max($red, $green, $blue);
    $chroma = $max - $min;

    $hue = 0;
    $saturation = 0;
    $lightness = $max * 100;

    // Do nothing if chroma is 0.
    if (!$chroma) {
      $saturation = $chroma / $max * 100;

      $hue = match ($min) {
        $red => 3 - (($green - $blue) / $chroma),
        $green => 5 - (($blue - $red) / $chroma),
        default => 1 - (($red - $green) / $chroma),
      // @codingStandardsIgnoreStart
      // Drupal Coding Standards fails to validate 'match' statement.
      };
      // @codingStandardsIgnoreEnd

      $hue *= 60;
    }

    return [
      'h' => $hue,
      's' => $saturation,
      'l' => $lightness,
    ];
  }

  /**
   * Converts HSL to RGB.
   *
   * @param int $hue
   *   The hue value.
   * @param int $saturation
   *   The saturation value.
   * @param int $lightness
   *   The lightness value.
   *
   * @return array
   *   An array with 'red', 'green' and 'blue' values.
   *
   * @see https://en.wikipedia.org/wiki/HSL_and_HSV#HSL_to_RGB_alternative
   */
  public static function hslToRgb(int $hue, int $saturation, int $lightness): array {
    $saturation /= 100;
    $lightness /= 100;
    $a = $saturation * \min($lightness, 1 - $lightness);
    $convert = static function ($n) use ($a, $hue, $lightness) {
      $k = ($n + $hue / 30) % 12;
      return $lightness - $a * \max(\min($k - 3, 9 - $k, 1), -1);
    };
    return [
      'red' => $convert(0),
      'green' => $convert(8),
      'blue' => $convert(4),
    ];
  }

}
