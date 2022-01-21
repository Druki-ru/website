<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a druki content contributors list.
 *
 * @Block(
 *   id = "druki_content_contributors",
 *   admin_label = @Translation("Contributors"),
 *   category = @Translation("Druki content"),
 *   context_definitions = {
 *     "druki_content" = @ContextDefinition("entity:druki_content", label = @Translation("Druki Content"), required =
 *   TRUE)
 *   }
 * )
 */
final class ContributorsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * An extra field display plugin manager.
   */
  protected ExtraFieldDisplayManagerInterface $extraFieldDisplayManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->extraFieldDisplayManager = $container->get('plugin.manager.extra_field_display');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'context_mapping' => [
        'druki_content' => '@druki_content.druki_content_route_context:druki_content',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    if (!$this->extraFieldDisplayManager->hasDefinition('contributors_and_authors')) {
      return [];
    }

    $build = [
      '#cache' => [
        'tags' => ['druki_author_list'],
      ],
    ];

    /** @var \Drupal\druki_content\Entity\ContentInterface $content */
    $content = $this->getContextValue('druki_content');
    /** @var \Drupal\extra_field\Plugin\ExtraFieldDisplayInterface $extra_field */
    $extra_field = $this->extraFieldDisplayManager->createInstance('contributors_and_authors');
    $extra_field->setEntity($content);
    $elements = $extra_field->view($content);
    if (!empty($elements)) {
      $build['content'] = $elements;
    }

    return $build;
  }

}
