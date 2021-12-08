<?php

namespace Drupal\druki_author\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\druki\Utility\Color;
use Drupal\druki_author\Entity\AuthorInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;

/**
 * Provides an extra field to display colored avatar.
 *
 * @ExtraFieldDisplay(
 *   id = "color_avatar",
 *   label = @Translation("Color avatar"),
 *   bundles = {
 *     "druki_author.*",
 *   },
 *   visible = TRUE,
 * )
 */
final class ColorAvatar extends ExtraFieldDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    \assert($entity instanceof AuthorInterface);
    $background_color = Color::textToHsl($entity->id());
    $background_color_hsl = 'hsl(' . $background_color['h'] . 'deg ' . $background_color['s'] . '% ' . $background_color['l'] . '%)';
    $background_color_rgb = Color::hslToRgb($background_color['h'], $background_color['s'], $background_color['l']);
    $background_color_hex = Color::rgbToHex($background_color_rgb);

    $initials_color_light = 'hsl(' . $background_color['h'] . 'deg ' . $background_color['s'] . '% 95%)';
    $initials_color_dark = 'hsl(' . $background_color['h'] . 'deg ' . $background_color['s'] . '% 5%)';
    $initials_color = Color::colorContrast($background_color_hex, $initials_color_light, $initials_color_dark);

    $initial_parts = [
      $entity->getNameGiven()[0],
      $entity->getNameFamily()[0],
    ];

    return [
      '#theme' => 'druki_avatar_placeholder',
      '#background_color' => $background_color_hsl,
      '#initials' => \implode('', $initial_parts),
      '#initials_color' => $initials_color,
    ];
  }

}
