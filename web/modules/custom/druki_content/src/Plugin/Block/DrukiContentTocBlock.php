<?php

namespace Drupal\druki_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a druki content toc block.
 *
 * @Block(
 *   id = "druki_content_toc",
 *   admin_label = @Translation("Druki content TOC"),
 *   category = @Translation("Druki content")
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
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];

    if ($druki_content = $this->getDrukiContentFromPage()) {
      $headings = $druki_content->get('content')->filter(function ($item) {
        return $item->entity->bundle() == 'druki_heading';
      });

      if (!$headings->isEmpty()) {
        $build['toc'] = [
          '#theme' => 'druki_content_toc',
          '#druki_content' => $druki_content,
        ];
      }
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

  /**
   * {@inheritDoc}
   */
  public function getCacheTags(): array {
    $cache_tags = [];

    if ($druki_content = $this->getDrukiContentFromPage()) {
      $cache_tags = Cache::mergeTags($cache_tags, $druki_content->getCacheTags());
    }

    return Cache::mergeTags(
      parent::getCacheTags(),
      $cache_tags
    );
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

}
