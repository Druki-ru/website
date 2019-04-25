<?php

namespace Drupal\druki_category\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\druki_category\Service\CategoryNavigation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a category navigation block.
 *
 * @Block(
 *   id = "druki_category_navigation",
 *   admin_label = @Translation("Category navigation"),
 *   category = @Translation("Druki Category")
 * )
 */
class CategoryNavigationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The category navigation.
   *
   * @var \Drupal\druki_category\Service\CategoryNavigation
   */
  protected $categoryNavigation;

  /**
   * Constructs a new CategoryNavigationBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\druki_category\Service\CategoryNavigation $category_navigation
   *   The category navigation.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    CategoryNavigation $category_navigation
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->categoryNavigation = $category_navigation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('druki_category.navigation')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];
    $links = $this->categoryNavigation->getCategoryLinksFromRoute();

    if ($links) {
      $build['navigation'] = [
        '#theme' => 'druki_category_navigation',
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
      $category_latin = $this->transliteration()->transliterate($category_area);
      $cache_tags[] = 'druki_category_navigation:' . $category_latin;
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
