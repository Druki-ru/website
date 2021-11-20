<?php

declare(strict_types=1);

namespace Drupal\druki\Controller;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\search\SearchPageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides custom implementation for /search page.
 *
 * @see \Drupal\druki\EventSubscriber\RouteSubscriber
 */
final class SearchController implements ContainerInjectionInterface {

  /**
   * The messenger service.
   */
  protected MessengerInterface $messenger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = new self();
    $instance->messenger = $container->get('messenger');
    return $instance;
  }

  /**
   * Builds the search page.
   *
   * @return array
   *   An array with search results.
   */
  public function view(Request $request, SearchPageInterface $entity): array {
    $build = [];
    $search_plugin = $entity->getPlugin();

    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($entity);
    $cache->addCacheContexts(['url.query_args:keys']);
    if ($search_plugin instanceof CacheableDependencyInterface) {
      $cache->addCacheableDependency($search_plugin);
    }
    if ($search_plugin->getType()) {
      $cache->addCacheTags([
        'search_index',
        'search_index:' . $search_plugin->getType(),
      ]);
    }

    if ($request->query->has('keys')) {
      $keys = \trim($request->query->get('keys'));
      $search_plugin->setSearch($keys, $request->query->all(), $request->attributes->all());
    }

    $build['#title'] = $search_plugin->suggestedTitle();
    $results = [];
    if ($request->query->has('keys')) {
      if ($search_plugin->isSearchExecutable()) {
        $results = $search_plugin->buildResults();
      }
      else {
        $this->messenger->addError(new TranslatableMarkup('Please enter some keywords.'));
      }
    }
    else {
      $this->messenger->addError(new TranslatableMarkup('Please enter some keywords.'));
    }

    $build['search_results'] = [
      '#theme' => 'druki_search_results',
      '#results' => $results,
    ];

    $build['pager'] = [
      '#type' => 'pager',
    ];

    $cache->applyTo($build);

    return $build;
  }

}
