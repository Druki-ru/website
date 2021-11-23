<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki\Repository\MediaImageRepositoryInterface;
use Drupal\druki_content\Data\ContentElementInterface;
use Drupal\druki_content\Data\ContentImageElement;
use Drupal\druki_content\Repository\ContentMediaImageRepository;
use Drupal\file\FileStorage;

/**
 * Provides render array builder for image element.
 */
final class ContentImageElementRenderArrayBuilder extends ContentElementRenderArrayBuilderBase {

  /**
   * The content media image repository.
   */
  protected MediaImageRepositoryInterface $imageRepository;

  /**
   * The file storage.
   */
  protected FileStorage $fileStorage;

  /**
   * Constructs a new ContentImageElementRenderArrayBuilder object.
   *
   * @param \Drupal\druki\Repository\MediaImageRepositoryInterface $image_repository
   *   The media image repository.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(MediaImageRepositoryInterface $image_repository, EntityTypeManagerInterface $entity_type_manager) {
    $this->imageRepository = $image_repository;
    $this->fileStorage = $entity_type_manager->getStorage('file');
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ContentElementInterface $element): bool {
    return $element instanceof ContentImageElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ContentElementInterface $element, array $children_render_array = []): array {
    \assert($element instanceof ContentImageElement);
    $media = $this->imageRepository->saveByUri($element->getSrc(), $element->getAlt());
    if (!$media) {
      return [];
    }
    $source_field = $media->getSource()->getConfiguration()['source_field'];
    /** @var \Drupal\file\FileInterface $file */
    $file = $media->get($source_field)->first()->get('entity')->getValue();
    if (!$file) {
      return [];
    }

    $build = [
      '#theme' => 'druki_photoswipe_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $element->getAlt(),
      '#responsive_image_style_id' => 'paragraph_druki_image_thumbnail',
      '#photoswipe_image_style_id' => 'paragraph_druki_image_big_image',
    ];

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($media);
    $cache->addCacheableDependency($file);
    $cache->applyTo($build);

    return $build;
  }

}
