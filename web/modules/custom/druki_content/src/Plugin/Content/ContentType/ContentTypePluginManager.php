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

}
