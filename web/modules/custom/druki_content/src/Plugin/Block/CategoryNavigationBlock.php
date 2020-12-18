<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
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
   * The druki content storage.
   *
   * @var \Drupal\druki_content\Entity\Handler\Storage\DrukiContentStorage
   */
  protected $contentStorage;

  /**
   * The static cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $staticCache;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->contentStorage = $container->get('entity_type.manager')->getStorage('druki_content');
    $instance->staticCache = $container->get('cache.static');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account): AccessResultInterface {
    $druki_content = $this->getDrukiContent();
    return AccessResult::allowedIf(!$druki_content->get('category')->isEmpty());
  }

  /**
   * Gets druki content to work with.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The druki content entity.
   */
  protected function getDrukiContent(): DrukiContentInterface {
    return $this->getContextValue('druki_content');
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
    $build['content'] = [
      '#theme' => 'druki_content_category_navigation',
      '#links' => $this->prepareLinks(),
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    $druki_content = $this->getDrukiContent();
    return $druki_content->get('category')->first()->getCategoryArea();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return Cache::mergeContexts(
      parent::getCacheContexts(),
      ['url.path'],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    $druki_content = $this->getDrukiContent();
    if ($druki_content->get('category')->isEmpty()) {
      return parent::getCacheTags();
    }

    $cache_tags = [];
    $cache_tags[] = 'druki_category_navigation:' . Crypt::hashBase64($this->getCategoryArea());
    foreach ($this->findContentForCategory() as $content) {
      $cache_tags = Cache::mergeTags($cache_tags, $content->getCacheTags());
    }

    return Cache::mergeTags(
      parent::getCacheTags(),
      $cache_tags,
    );
  }

  /**
   * Gets category area name.
   *
   * @return string
   *   The category area name.
   */
  protected function getCategoryArea(): string {
    $druki_content = $this->getDrukiContent();
    return $druki_content->get('category')->first()->getCategoryArea();
  }

  /**
   * Prepare links for category.
   *
   * @return array
   *   An array with links from category.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  protected function prepareLinks(): array {
    $links = [];
    foreach ($this->findContentForCategory() as $category_entity) {
      $links[] = [
        'url' => $category_entity->toUrl(),
        'text' => $category_entity->get('category')->first()->getCategoryItemTitle(),
      ];
    }
    return $links;
  }

  /**
   * Search for content of the same category.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface[]
   *   An array with content entities.
   */
  protected function findContentForCategory(): array {
    $cache_id = self::class . ':' . __METHOD__;
    if ($cache = $this->staticCache->get($cache_id)) {
      $result = $cache->data;
    }
    else {
      $druki_content = $this->getDrukiContent();
      $query = $this->contentStorage->getQuery()
        ->condition('category.area', $this->getCategoryArea())
        ->sort('category.order');
      if ($druki_content->hasField('core') && !$druki_content->get('core')->isEmpty()) {
        $query->condition('core', $druki_content->getCore());
      }

      $content_ids = $query->execute();
      $result = $this->contentStorage->loadMultiple($content_ids);
      $this->staticCache->set($cache_id, $result);
    }

    return $result;
  }

}
