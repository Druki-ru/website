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

  /**
   * {@inheritdoc}
   *
   * @todo Remove this method. It is used for druki_content_update_9302() update
   *   when druki_content entity bundle has been added. Since there was no any
   *   bundle previously and we using 'plugin' bundles, this will request for
   *   $plugin_id = '' (empty string), and we fallback to document plugin,
   *   because it represents default one.
   */
  protected function doGetDefinition(array $definitions, $plugin_id, $exception_on_invalid) {
    if (isset($definitions[$plugin_id])) {
      return $definitions[$plugin_id];
    }
    return $definitions['document'];
  }

}
