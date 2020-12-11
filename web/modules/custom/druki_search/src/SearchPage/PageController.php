<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides search page controller.
 *
 * @todo refactor it.
 */
final class PageController implements ContainerInjectionInterface {

  /**
   * The query helper.
   *
   * @var \Drupal\druki_search\SearchPage\QueryHelper
   */
  protected $queryHelper;

  /**
   * The amount of results per page.
   *
   * @var int
   */
  protected $limit = 10;

  /**
   * The request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The pager manager.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * Constructs a new PageController object.
   *
   * @param \Drupal\druki_search\SearchPage\QueryHelper $query_helper
   *   The query helper.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param \Drupal\Core\Pager\PagerManagerInterface $pager_manager
   *   The pager manager.
   */
  public function __construct(QueryHelper $query_helper, Request $request, PagerManagerInterface $pager_manager) {
    $this->queryHelper = $query_helper;
    $this->request = $request;
    $this->pagerManager = $pager_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('druki_search.page.query_helper'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('pager.manager'),
    );
  }

  /**
   * Builds page response.
   *
   * @return array
   *   The render array.
   *
   * @throws \Drupal\search_api\SearchApiException
   */
  public function build(): array {
    $keys = $this->request->get('text', NULL);
    if (\mb_strlen($keys) == 0) {
      $keys = NULL;
      $results = [];
      $result_items = [];
    }
    else {
      $results = $this->doSearch($keys);
      $result_items = $this->prepareSearchResults($results);
    }

    $build = [];
    if (!$keys) {
      $build['#title'] = new TranslatableMarkup('Search');
    }
    elseif (empty($results)) {
      $build['#title'] = new TranslatableMarkup('No results found for "%keys"', ['%keys' => $keys]);
    }
    else {
      $build['#title'] = new TranslatableMarkup('Search results for "%keys"', ['%keys' => $keys]);
    }

    if (empty($results)) {
      $build['page'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-search-page'],
        ],
      ];
    }

    if (!$keys) {
      $search_results = [
        '#type' => 'container',
        '#markup' => new TranslatableMarkup("You didn't enter a search query."),
        '#attributes' => [
          'class' => 'druki-search-page__supporting-text',
        ],
      ];
    }
    elseif (empty($result_items)) {
      $search_results = [
        '#type' => 'container',
        '#markup' => new TranslatableMarkup("No results found."),
        '#attributes' => [
          'class' => 'druki-search-page__supporting-text',
        ],
      ];
    }
    else {
      $search_results = $this->prepareSearchResults($results);
    }

    $build['page']['search_results'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['druki-search-page__results'],
      ],
      0 => $search_results,
    ];

    $build['page']['pager'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['druki-search-page__pager'],
      ],
      0 => [
        '#type' => 'pager',
        '#quantity' => 3,
      ],
    ];

    $build['#cache'] = [
      'keys' => [
        'druki_search',
        'search_page',
        'results',
      ],
      'contexts' => [
        'url.path',
        'url.query_args',
      ],
      'max-age' => 60 * 60 * 24,
    ];

    return $build;
  }

  /**
   * Do real search for results.
   *
   * @param string $keys
   *   The search keyword.
   *
   * @return \Drupal\search_api\Item\ItemInterface[]
   *   The result set.
   *
   * @throws \Drupal\search_api\SearchApiException
   */
  protected function doSearch(string $keys): array {
    $total_results = $this->getTotalResultItems($keys);
    $this->pagerManager->createPager($total_results, $this->limit);

    return $this->queryHelper
      ->getQuery(QueryHelper::FILTERED)
      ->range($this->pagerManager->getPager()->getCurrentPage() * $this->limit, $this->limit)
      ->keys($keys)
      ->execute()
      ->getResultItems();
  }

  /**
   * Gets total query result items.
   *
   * @param string $keys
   *   The search keyword.
   *
   * @return int
   *   The amount of result items.
   *
   * @throws \Drupal\search_api\SearchApiException
   */
  protected function getTotalResultItems(string $keys): int {
    return $this->queryHelper
      ->getQuery(QueryHelper::FILTERED)
      ->keys($keys)
      ->execute()
      ->getResultCount();
  }

  /**
   * Prepare render array with result items from result set.
   *
   * @param \Drupal\search_api\Item\ItemInterface[] $results
   *   The array of search results.
   *
   * @return array
   *   The array with render arrays of results.
   */
  protected function prepareSearchResults(array $results): array {
    $search_results = [];
    /** @var \Drupal\search_api\Item\Item $result */
    foreach ($results as $result) {
      $url = Url::fromUserInput($result->getField('url')->getValues()[0]);
      $search_results[] = [
        '#type' => 'druki_search_result',
        '#title' => $result->getField('title')->getValues()[0],
        '#url' => $url,
        '#display_url' => $url->setAbsolute()->toString(),
        '#supporting_text' => Markup::create($result->getExcerpt()),
        '#theme_wrappers' => [
          'container' => [
            '#attributes' => [
              'class' => ['druki-search-page__result'],
            ],
          ],
        ],
      ];
    }

    return $search_results;
  }

}
