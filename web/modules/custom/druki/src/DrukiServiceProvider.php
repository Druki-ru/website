<?php

declare(strict_types=1);

namespace Drupal\druki;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\druki\DependencyInjection\RegisterDrukiEntitySyncQueueCompilerPass;

/**
 * Provides custom dynamic services and definitions.
 */
final class DrukiServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container): void {
    $container->addCompilerPass(new RegisterDrukiEntitySyncQueueCompilerPass());
  }

}
