<?php

namespace Drupal\druki_content\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a deriver for Druki Content actions.
 */
final class ContentActionDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The entity type manager.
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The base plugin ID.
   */
  protected string $basePluginId;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id): self {
    $instance = new static();
    $instance->basePluginId = $base_plugin_id;
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    /** @var \Drupal\Core\Entity\EntityTypeInterface $druki_entity_type */
    $druki_entity_type = $this->entityTypeManager->getDefinition('druki_content');

    if ($druki_entity_type->hasLinkTemplate('delete-all-form') && $druki_entity_type->hasHandlerClass('form', 'delete-all')) {
      $this->derivatives['delete_all'] = [
        'title' => new TranslatableMarkup('Delete all druki content'),
        'route_name' => 'entity.druki_content.delete_all',
        'appears_on' => [
          'entity.druki_content.collection',
        ],
      ];
    }

    if ($druki_entity_type->hasLinkTemplate('invalidate-all-form') && $druki_entity_type->hasHandlerClass('form', 'invalidate-all')) {
      $this->derivatives['invalidate_all'] = [
        'title' => new TranslatableMarkup('Invalidate all content'),
        'route_name' => 'entity.druki_content.invalidate_all_form',
        'appears_on' => [
          'entity.druki_content.collection',
        ],
      ];
    }

    return $this->derivatives;
  }

}
