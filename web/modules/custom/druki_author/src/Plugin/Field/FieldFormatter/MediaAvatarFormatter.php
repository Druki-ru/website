<?php

declare(strict_types=1);

namespace Drupal\druki_author\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Provides field formatter for author avatars.
 *
 * Author can have a media with a picture. But this is optional featue, but we
 * expect to display avatar in many places of the website. For authors that
 * doesn't want to provide a picture, we should provide a static avatar
 * placeholder.
 *
 * This formatter display an image from media reference if its presented, but if
 * doesn't it displays a placeholder with initials.
 *
 * @FieldFormatter(
 *   id = "druki_author_media_avatar",
 *   label = @Translation("Media avatar"),
 *   field_types = {
 *     "entity_reference",
 *   },
 * )
 */
final class MediaAvatarFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition): bool {
    $is_media = $field_definition->getSetting('target_type') == 'media';
    $is_author_entity = $field_definition->getTargetEntityTypeId() == 'druki_author';
    return $is_media && $is_author_entity;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    if ($items->isEmpty()) {
      // @todo Build a placeholder for an empty field.
    }
    else {
      // @todo Build image style result, but if media is corrupted, fallback to
      //    placeholder.
    }

    return $elements;
  }

}
