<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a druki content links block.
 *
 * @Block(
 *   id = "druki_content_links",
 *   admin_label = @Translation("Druki Content Links"),
 *   category = @Translation("Druki Content")
 * )
 */
class LinksBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  private $routeMatch;

  /**
   * Constructs a new LinksBlock object.
   *
   * @param array $configuration
   *   The plugin configurations.
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   THe route match.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity = $this->getContentFromCurrentPage();

    if (!$entity) {
      return [];
    }

    return [
      '#type' => 'druki_content_links',
      '#entity' => $entity,
    ];
  }

  /**
   * Gets druki content from current page.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|null
   *   The entity object, NULL if not found.
   */
  protected function getContentFromCurrentPage(): ?DrukiContentInterface {
    return $this->routeMatch->getParameter('druki_content');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(
      parent::getCacheContexts(),
      ['url.path']
    );
  }

}
