<?php

declare(strict_types=1);

namespace Drupal\druki\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides deriver for entity sync queue workers.
 */
final class EntitySyncQueueWorkerDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * An array with entity sync queue names.
   */
  protected array $queueNames;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id): self {
    $instance = new self();
    $instance->queueNames = $container->getParameter('druki.entity_sync_queues');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    foreach ($this->queueNames as $queue_name) {
      $this->derivatives[$queue_name] = $base_plugin_definition;
      $this->derivatives[$queue_name]['id'] = $queue_name;
    }
    return $this->derivatives;
  }

}
