<?php

declare(strict_types=1);

namespace Drupal\druki\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds 'druki.entity_sync_queues' parameter value.
 *
 * Looking for services tagged as 'druki_entity_sync_queue' and gets their
 * 'queue_name' value. All found queue names will be set as parameter value
 * 'druki.entity_sync_queues'. Also it pass the queue name to queue manager
 * factory.
 */
final class RegisterDrukiEntitySyncQueueCompilerPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container): void {
    $druki_entity_sync_queues = [];
    foreach ($container->findTaggedServiceIds('druki_entity_sync_queue') as $id => $attributes) {
      if (!isset($attributes[0]['queue_name'])) {
        continue;
      }

      $queue_name = $attributes[0]['queue_name'];
      $druki_entity_sync_queues[] = $queue_name;

      $service = $container->getDefinition($id);
      $service->setArgument(0, $queue_name);
    }
    $container->setParameter('druki.entity_sync_queues', $druki_entity_sync_queues);
  }

}
