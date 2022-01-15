<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\Content\ContentType;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a plugin manager for 'druki_content' entity bundle plugins.
 */
final class ContentTypePluginManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/Content/ContentType',
      $namespaces,
      $module_handler,
      'Drupal\druki_content\Plugin\Content\ContentType\ContentTypeInterface',
      'Drupal\druki_content\Annotation\ContentType',
    );

    // We don't actually need it, but this is a best practice, so it's added.
    // Delete this line will lead to PHPStan failing.
    $this->alterInfo('druki_content_type_info');
    $this->setCacheBackend($cache_backend, 'druki_content_type_plugins');
  }

  /**
   * {@inheritdoc}
   *
   * @todo Remove this method. It is used for druki_content_update_9302() update
   *   when druki_content entity bundle has been added. Since there was no any
   *   bundle previously and we using 'plugin' bundles, this will request for
   *   $plugin_id = '' (empty string), and we fallback to 'druki_content'
   *   plugin, because it represents default one.
   */
  protected function doGetDefinition(array $definitions, $plugin_id, $exception_on_invalid) {
    if (isset($definitions[$plugin_id])) {
      return $definitions[$plugin_id];
    }
    return $definitions['druki_content'];
  }

}
