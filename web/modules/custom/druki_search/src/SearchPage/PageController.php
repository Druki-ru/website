<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PageController implements ContainerInjectionInterface {

  /**
   * The query helper.
   *
   * @var \Drupal\druki_search\SearchPage\QueryHelper
   */
  protected $queryHelper;

  protected $limit = 20;

  /**
   * The request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  public function __construct(QueryHelper $query_helper, Request $request, FormBuilderInterface $form_builder) {
    $this->queryHelper = $query_helper;
    $this->request = $request;
    $this->formBuilder = $form_builder;
  }

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('druki_search.page.query_helper'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('form_builder')
    );
  }

  public function build() {
    $search_text = $this->request->get('text', NULL);

    $query = $this->queryHelper->getQuery(QueryHelper::FILTERED);

    $total_items = $this->queryHelper->getQuery(QueryHelper::FILTERED)->execute()->getResultCount();
    $current_page = pager_default_initialize($total_items, $this->limit);
    $query->range($current_page * $this->limit, $this->limit);
    $query->keys($search_text);
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
      'search_form' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-search-page__form'],
        ],
        0 => $this->formBuilder->getForm('Drupal\druki_search\SearchPage\SearchForm'),
      ],
      'search_results' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-search-page__results'],
        ],
        0 => $search_results,
      ],
      'pager' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['druki-search-page__pager'],
        ],
        0 => [
          '#type' => 'pager',
        ],
      ],
      '#cache' => [
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
      ],
    ];
  }
}
