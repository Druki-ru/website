<?php

namespace Drupal\druki\Helper;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides helper for responsive images.
 */
final class ResponsiveImageStyleHelper {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The cache bin.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Constructs a new ResponsiveImageStyleHelper object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache bin.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, CacheBackendInterface $cache) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cache = $cache;
  }

  /**
   * Gets available responsive image styles as options list.
   *
   * @return array
   *   An array with responsive image styles keyed by id.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getOptions(): array {
    $cid = __METHOD__;
    $options = [];
    if ($cache = $this->cache->get($cid)) {
      $options = $cache->data;
    }
    else {
      $style_storage = $this->entityTypeManager->getStorage('responsive_image_style');
      $styles = $style_storage->loadMultiple();
      /** @var \Drupal\responsive_image\ResponsiveImageStyleInterface $responsive_image_style */
      foreach ($styles as $responsive_image_style) {
        $options[$responsive_image_style->id()] = $responsive_image_style->label();
      }
      $this->cache->set($cid, $options);
    }
    return $options;
  }

}
