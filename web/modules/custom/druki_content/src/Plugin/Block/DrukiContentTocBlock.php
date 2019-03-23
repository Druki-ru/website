<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a druki content toc block.
 *
 * @Block(
 *   id = "druki_content_toc",
 *   admin_label = @Translation("Druki Content TOC"),
 *   category = @Translation("Druki Content")
 * )
 */
class DrukiContentTocBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The route match.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $routeMatch;

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CurrentRouteMatch $current_route_match) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $current_route_match;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
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
  public function build(): array {
    $build = [];

    if ($druki_content = $this->getDrukiContentFromPage()) {
      $build['toc'] = [
        '#theme' => 'druki_content_toc',
        '#druki_content' => $druki_content,
      ];
    }

    return $build;
  }

  /**
   * Gets druki content from page.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|null
   *   The entity if fount, NULL otherwise.
   */
  protected function getDrukiContentFromPage(): ?DrukiContentInterface {
    foreach ($this->routeMatch->getParameters() as $parameter) {
      if ($parameter instanceof DrukiContentInterface) {
        return $parameter;
      }
    }

    return NULL;
  }

}
