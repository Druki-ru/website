<?php

declare(strict_types=1);

namespace Drupal\druki_author\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

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
 *   id = "druki_author_avatar",
 *   label = @Translation("Avatar"),
 *   field_types = {
 *     "entity_reference",
 *   },
 * )
 */
final class AvatarFormatter extends FormatterBase {

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
  public static function defaultSettings(): array {
    return [
      'image_style' => NULL,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    /** @var \Drupal\druki_author\Entity\AuthorInterface $author */
    $author = $items->getEntity();
    if ($items->isEmpty() || !$this->getSetting('image_style')) {
      $element = $this->buildPlaceholder($author->id());
    }
    else {
      /** @var \Drupal\media\MediaInterface $media */
      $media = $items->first()->get('entity')->getValue();
      $source_field = $media->getSource()->getConfiguration()['source_field'];
      if ($media->get($source_field)->isEmpty()) {
        $element = $this->buildPlaceholder($author->id());
      }
      else {
        /** @var \Drupal\file\FileInterface $file */
        $file = $media->get($source_field)->first()->get('entity')->getValue();
        $alt = (string) new TranslatableMarkup('Avatar of the author @author', [
          '@author' => $author->id(),
        ]);
        $element = $this->buildImage($file->getFileUri(), $alt);
      }
    }

    return [$element];
  }

  /**
   * Builds an avatar placeholder render array.
   *
   * @param string $username
   *   The username used for placeholder.
   *
   * @return array
   *   An avatar placeholder.
   */
  protected function buildPlaceholder(string $username): array {
    return [
      '#type' => 'druki_avatar_placeholder',
      '#username' => $username,
    ];
  }

  /**
   * Builds an image with specific image style.
   *
   * @param string $uri
   *   The image URI.
   * @param string $alt
   *   The image alt.
   *
   * @return array
   *   A renderable array with image.
   */
  protected function buildImage(string $uri, string $alt): array {
    return [
      '#theme' => 'image_style',
      '#style_name' => $this->getSetting('image_style'),
      '#uri' => $uri,
      '#attributes' => [
        'alt' => $alt,
      ],
    ];
  }

}
