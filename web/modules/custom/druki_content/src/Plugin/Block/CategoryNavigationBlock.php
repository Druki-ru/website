<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a category navigation block.
 *
 * @Block(
 *   id = "druki_category_navigation",
 *   admin_label = @Translation("Category navigation"),
 *   category = @Translation("Druki Category"),
 *   context_definitions = {
 *     "druki_content" = @ContextDefinition("entity:druki_content", label = @Translation("Druki Content"), required = TRUE),
 *   }
 * )
 */
final class CategoryNavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The category navigation.
   *
   * @var \Drupal\druki_content\Category\CategoryNavigation
   */
  protected $categoryNavigation;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->categoryNavigation = $container->get('druki_content.category.navigation');

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
    $build = [];
    $links = $this->categoryNavigation->getCategoryLinksFromRoute();

    if ($links) {
      $build['navigation'] = [
        '#theme' => 'druki_content_category_navigation',
        '#links' => $links,
      ];
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    if ($category_area = $this->categoryNavigation->getCategoryAreaFromRoute()) {
      return $category_area;
    }

    return parent::label();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    $cache_contexts = [
      'url.path',
    ];

    return Cache::mergeContexts(
      parent::getCacheContexts(),
      $cache_contexts
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheTags() {
    $cache_tags = [];

    if ($category_area = $this->categoryNavigation->getCategoryAreaFromRoute()) {
      $cache_tags[] = 'druki_category_navigation:' . Crypt::hashBase64($category_area);
    }

    $links = $this->categoryNavigation->getCategoryLinksFromRoute();
    if ($links) {
      foreach ($links as $link) {
        $cache_tags = Cache::mergeTags($cache_tags, $link['cache_tags']);
      }
    }

    return Cache::mergeTags(
      parent::getCacheTags(),
      $cache_tags
    );
  }

}
