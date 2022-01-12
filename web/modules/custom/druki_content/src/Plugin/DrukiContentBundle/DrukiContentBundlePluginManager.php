<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\DrukiContentBundle;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a plugin manager for 'druki_content' entity bundle plugins.
 */
final class DrukiContentBundlePluginManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/DrukiContentBundle',
      $namespaces,
      $module_handler,
      'Drupal\druki_content\Plugin\DrukiContentBundle\DrukiContentBundleInterface',
      'Drupal\druki_content\Annotation\DrukiContentBundle',
    );

    $this->setCacheBackend($cache_backend, 'druki_content_bundle_plugins');
  }

}
