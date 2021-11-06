<?php

namespace Drupal\druki_content\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a deriver for Druki Content menu links.
 */
final class DrukiContentMenuLinkDeriver extends DeriverBase implements ContainerDeriverInterface {

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

    $this->derivatives['group'] = [
      'title' => new TranslatableMarkup('Druki content'),
      'description' => new TranslatableMarkup('Settings for Druki content entity'),
      'route_name' => 'druki_content.admin',
      'parent' => 'druki.admin',
    ];

    if ($druki_entity_type->hasLinkTemplate('settings') && $druki_entity_type->hasHandlerClass('form', 'settings')) {
      $this->derivatives['settings'] = [
        'title' => new TranslatableMarkup('Entity settings'),
        'description' => new TranslatableMarkup('Configure a Druki content entity type.'),
        'route_name' => 'entity.druki_content.settings',
        'parent' => "{$this->basePluginId}:group",
      ];
    }

    if ($druki_entity_type->hasLinkTemplate('collection') && $druki_entity_type->hasHandlerClass('list_builder')) {
      $this->derivatives['collection'] = [
        'title' => new TranslatableMarkup('List of content'),
        'description' => new TranslatableMarkup('List of druki content.'),
        'route_name' => 'entity.druki_content.collection',
        'parent' => "{$this->basePluginId}:group",
      ];
    }

    if ($druki_entity_type->hasLinkTemplate('sync') && $druki_entity_type->hasHandlerClass('form', 'sync')) {
      $this->derivatives['sync'] = [
        'title' => new TranslatableMarkup('Content synchronization'),
        'description' => new TranslatableMarkup('Run synchronization manually or track current progress.'),
        'route_name' => 'entity.druki_content.sync',
        'parent' => "{$this->basePluginId}:group",
      ];
    }

    $this->derivatives['source_content_settings'] = [
      'title' => new TranslatableMarkup('Source content settings'),
      'description' => new TranslatableMarkup('Configure content source URI and remote URL.'),
      'route_name' => 'druki_content.content_source_settings',
      'parent' => "{$this->basePluginId}:group",
    ];

    $this->derivatives['content_webhook_settings'] = [
      'title' => new TranslatableMarkup('Webhook settings'),
      'description' => new TranslatableMarkup('Configure webhooks to be able managing content remotely.'),
      'route_name' => 'druki_content.content_webhook_settings',
      'parent' => "{$this->basePluginId}:group",
    ];

    return $this->derivatives;
  }

}
