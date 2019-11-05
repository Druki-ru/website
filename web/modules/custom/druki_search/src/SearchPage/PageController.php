<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PageController implements ContainerInjectionInterface {

  /**
   * The query helper.
   *
   * @var \Drupal\druki_search\SearchPage\QueryHelper
   */
  protected $queryHelper;

  protected $limit = 20;

  public function __construct(QueryHelper $query_helper) {
    $this->queryHelper = $query_helper;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('druki_search.page.query_helper')
    );
  }

  public function build() {
    $query = $this->queryHelper->getQuery();
    $query->keys('ContainerInjectionInterface');
    $query->range(0, $this->limit);
    $results = $query->execute();

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

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['druki-search-page'],
      ],
      'search_results' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-search-page__results'],
        ],
        0 => $search_results,
      ],
      '#cache' => [
        'keys' => [
          'druki_search',
          'search_page',
        ],
        'contexts' => [
          'url.path',
          'url.query_args',
        ],
        'max-age' => 60 * 60 * 24,
      ],
    ];
  }
}
