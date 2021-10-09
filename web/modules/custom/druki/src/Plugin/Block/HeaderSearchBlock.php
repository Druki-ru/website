<?php

namespace Drupal\druki\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a header search block.
 *
 * @Block(
 *   id = "druki_header_search",
 *   admin_label = @Translation("Header search"),
 *   category = @Translation("Druki")
 * )
 */
final class HeaderSearchBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The request stack.
   */
  protected RequestStack $requestStack;

  /**
   * The current route match.
   */
  protected CurrentRouteMatch $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->requestStack = $container->get('request_stack');
    $instance->routeMatch = $container->get('current_route_match');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build['content'] = [
      '#theme' => 'druki_header_search',
      '#default_value' => $this->getDefaultValue(),
    ];

    return $build;
  }

  /**
   * Gets default value for search input.
   *
   * @return string
   *   The value for input.
   */
  protected function getDefaultValue(): string {
    $result = '';
    $request = $this->requestStack->getCurrentRequest();

    if ($request->query->has('text') && $this->routeMatch->getRouteName() == 'druki_search.page') {
      $result = $request->query->get('text');
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), [
      'url.path',
      'url.query_args:text',
    ]);
  }

}
