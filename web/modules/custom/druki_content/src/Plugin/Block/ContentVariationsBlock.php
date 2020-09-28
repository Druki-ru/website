<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a content variations notice block.
 *
 * @Block(
 *   id = "druki_content_variations",
 *   admin_label = @Translation("Other content variations"),
 *   category = @Translation("Custom"),
 *   context_definitions = {
 *     "druki_content" = @ContextDefinition("entity:druki_content", label = @Translation("Druki Content"), required = TRUE),
 *   }
 * )
 */
final class ContentVariationsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The druki content storage.
   *
   * @var \Drupal\druki_content\Entity\Handler\Storage\DrukiContentStorage
   */
  protected $drukiContentStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->drukiContentStorage = $container->get('entity_type.manager')->getStorage('druki_content');
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
  public function blockAccess(AccountInterface $account): AccessResult {
    $druki_content = $this->getDrukiContentFromContext();
    return AccessResult::allowedIf($this->hasOtherVariations($druki_content));
  }

  /**
   * Gets druki content entity from context.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The druki content entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function getDrukiContentFromContext(): DrukiContentInterface {
    return $this->getContext('druki_content')->getContextValue();
  }

  /**
   * Checks whether content has other differ variants expect current.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   *
   * @return bool
   *   TRUE if there are at least one variation about same topic, FALSE
   *   otherwise.
   */
  protected function hasOtherVariations(DrukiContentInterface $druki_content): bool {
    // The content without core can't have variations.
    if (!$druki_content->getCore()) {
      return FALSE;
    }

    // Don't call '::findOtherVariations' to simplify logic and reduce DB usage.
    $query = $this->drukiContentStorage->getQuery()
      ->condition('external_id', $druki_content->getExternalId())
      ->exists('core')
      ->condition('core', $druki_content->getCore(), '<>')
      ->count();

    return (bool) $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    $druki_content = $this->getDrukiContentFromContext();
    return Cache::mergeTags(parent::getCacheTags(), $druki_content->getCacheTagsToInvalidate());
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $cacheable_metadata = new CacheableMetadata();
    $druki_content = $this->getDrukiContentFromContext();
    $variations = $this->loadAllVariations($druki_content);
    $current_version = $druki_content->getCore();
    $other_variations = array_map(function (DrukiContentInterface $druki_content) use ($cacheable_metadata, $current_version) {
      $cacheable_metadata->addCacheableDependency($druki_content);
      if ($druki_content->getCore() != $current_version) {
        $element = $druki_content->toLink($druki_content->getCore())->toRenderable();
      }
      else {
        $element = [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $druki_content->getCore(),
          '#attributes' => [
            'class' => ['druki-content-variations__variation--active'],
          ],
        ];
      }
      $element['#attributes']['class'][] = 'druki-content-variations__variation';
      return $element;
    }, $variations);

    $build['content'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'druki-content-variations',
      ],
      'current_version' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-content-variations__label'],
        ],
        'content' => [
          '#markup' => 'Drupal',
        ],
      ],
      'other_versions' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-content-variations__variations'],
        ],
        'content' => $other_variations,
      ],
    ];
    return $build;
  }

  /**
   * Loads all content variations for specific topic.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface[]
   *   An array with all variations.
   */
  protected function loadAllVariations(DrukiContentInterface $druki_content): array {
    $content_ids = $this->drukiContentStorage->getQuery()
      ->condition('external_id', $druki_content->getExternalId())
      ->exists('core')
      ->sort('core', 'ASC')
      ->execute();
    return $this->drukiContentStorage->loadMultiple($content_ids);
  }

}
