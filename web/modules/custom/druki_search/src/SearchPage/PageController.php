<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Pager\PagerParameters;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\search_api\Query\ResultSetInterface;
use Drupal\search_api\Utility\QueryHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides search page controller.
 */
final class PageController implements ContainerInjectionInterface {

  /**
   * The amount of results per page.
   */
  protected int $limit = 10;

  /**
   * The pager manager.
   */
  protected PagerManagerInterface $pagerManager;

  /**
   * The request stack.
   */
  protected RequestStack $requestStack;

  /**
   * The query helper.
   */
  protected QueryHelperInterface $queryHelper;

  /**
   * The search API index.
   */
  protected EntityStorageInterface $indexStorage;

  /**
   * The pager parameters.
   */
  protected PagerParameters $pagerParameters;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = new self();
    $instance->indexStorage = $container->get('entity_type.manager')
      ->getStorage('search_api_index');
    $instance->queryHelper = $container->get('search_api.query_helper');
    $instance->requestStack = $container->get('request_stack');
    $instance->pagerManager = $container->get('pager.manager');
    $instance->pagerParameters = $container->get('pager.parameters');
    return $instance;
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
    $results = $this->execute();
    $build = [];
    if (!$this->isSearchExecutable()) {
      $build['#title'] = new TranslatableMarkup('Search');
      $build['content'] = [
        '#markup' => new TranslatableMarkup("You didn't enter a search query."),
      ];
    }
    elseif (empty($results)) {
      $build['#title'] = new TranslatableMarkup('No results found for "%keys"', ['%keys' => $this->getSearchKeywords()]);
      $build['content'] = [
        '#markup' => new TranslatableMarkup('No results found.'),
      ];
    }
    else {
      $build['#title'] = new TranslatableMarkup('Search results for "%keys"', ['%keys' => $this->getSearchKeywords()]);
      $build['content'] = [
        '#theme' => 'druki_search_results',
        '#items' => $results,
        '#pager' => [
          '#type' => 'pager',
        ],
      ];
    }

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
   * Run search.
   *
   * @return array
   *   The search results.
   */
  protected function execute(): array {
    if ($this->isSearchExecutable()) {
      $results = $this->findResults();

      if ($results->getResultCount()) {
        return $this->prepareResults();
      }
    }

    return [];
  }

  /**
   * Verifies that everything is fine to run search.
   *
   * @return bool
   *   TRUE if search can be run.
   */
  protected function isSearchExecutable(): bool {
    return !empty($this->getSearchKeywords());
  }

  /**
   * Gets search keywords.
   *
   * @return string|null
   *   The search keywords. NULL if not found or empty.
   */
  protected function getSearchKeywords(): ?string {
    return $this->requestStack->getCurrentRequest()->query->get('text');
  }

  /**
   * Querying results from search engine.
   *
   * @return \Drupal\search_api\Query\ResultSetInterface
   *   The result set.
   */
  protected function findResults(): ResultSetInterface {
    $index = $this->indexStorage->load('global');
    $query = $this->queryHelper->createQuery($index);
    $query->keys($this->getSearchKeywords());
    $query->range($this->pagerParameters->findPage() * $this->limit, $this->limit);
    $result = $query->execute();
    $this->pagerManager->createPager($result->getResultCount(), $this->limit);
    return $result;
  }

  /**
   * Prepares search results.
   *
   * @return array
   *   An array with processed search results.
   */
  protected function prepareResults(): array {
    $results = $this->findResults();
    $items = [];
    foreach ($results->getResultItems() as $result_item) {
      $url = Url::fromUserInput($result_item->getField('url')->getValues()[0]);
      $items[] = [
        'label' => $result_item->getField('title')->getValues()[0],
        'url' => $url,
        'url_absolute' => $url->setAbsolute()->toString(),
      ];
    }
    return $items;
  }

}
