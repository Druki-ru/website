<?php

declare(strict_types=1);

namespace Drupal\druki\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\druki\Utility\Color;

/**
 * A render element to present avatar placeholder.
 *
 * @RenderElement("druki_avatar_placeholder")
 */
final class AvatarPlaceholder extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo(): array {
    return [
      '#theme' => 'druki_avatar_placeholder',
      '#username' => NULL,
      '#pre_render' => [
        [$this, 'preRenderElement'],
      ],
    ];
  }

  /**
   * Builds render array for element.
   *
   * @param array $element
   *   The current render array.
   *
   * @return array
   *   The updated render array.
   */
  public function preRenderElement(array $element): array {
    $username = $element['#username'];

    $background_color = Color::textToHsl($username);
    $background_color_hsl = 'hsl(' . $background_color['h'] . 'deg ' . $background_color['s'] . '% ' . $background_color['l'] . '%)';
    $background_color_rgb = Color::hslToRgb($background_color['h'], $background_color['s'], $background_color['l']);
    $background_color_hex = Color::rgbToHex($background_color_rgb);

    $initials_color_light = 'hsl(' . $background_color['h'] . 'deg ' . $background_color['s'] . '% 95%)';
    $initials_color_dark = 'hsl(' . $background_color['h'] . 'deg ' . $background_color['s'] . '% 5%)';
    $initials_color = Color::colorContrast($background_color_hex, $initials_color_light, $initials_color_dark);

    $initial_parts = \explode(' ', $username);
    $initial_parts = \array_filter($initial_parts, static fn ($initial_part) => \preg_match('/[a-zA-Z0-9]/', $initial_part));
    $initial_parts = \array_splice($initial_parts, 0, 2);
    $initial_parts = \array_map(static fn ($initial_part) => $initial_part[0], $initial_parts);

    $element['#background_color'] = $background_color_hsl;
    $element['#initials_color'] = $initials_color;
    $element['#initials'] = \implode('', $initial_parts);

    return $element;
  }

}
