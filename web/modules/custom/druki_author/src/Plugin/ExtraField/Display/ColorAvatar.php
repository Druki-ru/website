<?php

namespace Drupal\druki_author\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
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
    return [
      '#type' => 'druki_avatar_placeholder',
      '#username' => $entity->id(),
    ];
  }

}
