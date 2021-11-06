<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\Search;

use Drupal\Core\Config\Config;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\PagerSelectExtender;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\Repository\DrukiContentStorage;
use Drupal\search\Plugin\SearchIndexingInterface;
use Drupal\search\Plugin\SearchPluginBase;
use Drupal\search\SearchIndexInterface;
use Drupal\search\SearchQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides search plugin for druki content.
 *
 * @SearchPlugin(
 *   id = "druki_content",
 *   title = @Translation("Druki content"),
 * )
 */
final class ContentSearch extends SearchPluginBase implements SearchIndexingInterface {

  /**
   * The current database connection.
   */
  protected Connection $database;

  /**
   * The search settings.
   */
  protected Config $searchSettings;

  /**
   * The search index.
   */
  protected SearchIndexInterface $searchIndex;

  /**
   * The content storage.
   */
  protected DrukiContentStorage $contentStorage;

  /**
   * The renderer.
   */
  protected RendererInterface $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->database = $container->get('database');
    $instance->searchSettings = $container->get('config.factory')->get('search.settings');
    $instance->searchIndex = $container->get('search.index');
    $instance->contentStorage = $container->get('entity_type.manager')->getStorage('druki_content');
    $instance->renderer = $container->get('renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(): array {
    if ($this->isSearchExecutable()) {
      $results = $this->findResults();

      if ($results) {
        return $this->prepareResults($results);
      }
    }

    return [];
  }

  /**
   * Queries to find search results.
   *
   * @return \Drupal\Core\Database\StatementInterface|null
   *   The results from search query execute().
   */
  public function findResults(): ?StatementInterface {
    $keys = $this->getKeywords();

    $query = $this->database
      ->select('search_index', 'i')
      ->extend(SearchQuery::class)
      ->extend(PagerSelectExtender::class);

    $query->searchExpression($keys, $this->getPluginId());

    // Add scoring by relevance.
    $query->addScore('i.relevance');

    // Add relevance by Drupal Core version.
    $query->leftJoin('druki_content_field_data', 'cfd', '[i].[sid] = [cfd].[internal_id] AND [i].[type] = :type', [
      ':type' => $this->getPluginId(),
    ]);
    $query->addExpression('(CASE WHEN [cfd].[core] IS NULL THEN 0 ELSE [cfd].[core] END)', 'calculated_drupal_core');
    // For some reason we can't use aliased expression here.
    $query->addScore('(CASE WHEN [cfd].[core] IS NULL THEN 0 ELSE [cfd].[core] END)');

    return $query
      ->fields('i', ['langcode'])
      ->groupBy('i.langcode')
      ->limit(10)
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getType(): ?string {
    return $this->getPluginId();
  }

  /**
   * {@inheritdoc}
   */
  public function updateIndex(): void {
    $limit = (int) $this->searchSettings->get('index.cron_limit');

    $query = $this->database->select('druki_content', 'dc');
    $query->addField('dc', 'internal_id');
    $query->leftJoin('search_dataset', 'sd', '[sd].[sid] = [dc].[internal_id] AND [sd].[type] = :type', [':type' => $this->getPluginId()]);
    $query->addExpression('CASE MAX([sd].[reindex]) WHEN NULL THEN 0 ELSE 1 END', 'ex');
    $query->addExpression('MAX([sd].[reindex])', 'ex2');
    $query->condition(
      $query->orConditionGroup()
        ->where('[sd].[sid] IS NULL')
        ->condition('sd.reindex', 0, '<>')
    );
    $query->orderBy('ex', 'DESC')
      ->orderBy('ex2')
      ->orderBy('dc.internal_id')
      ->groupBy('dc.internal_id')
      ->range(0, $limit);

    $content_ids = $query->execute()->fetchCol();
    if (!$content_ids) {
      return;
    }

    $words = [];
    try {
      $entities = $this->contentStorage->loadMultiple($content_ids);
      foreach ($entities as $entity) {
        $words += $this->indexContent($entity);
      }
    } finally {
      $this->searchIndex->updateWordWeights($words);
    }
  }

  /**
   * Indexes a single content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $content
   *   The content to index.
   *
   * @return array
   *   An array of words to update after indexing.
   */
  protected function indexContent(DrukiContentInterface $content): array {
    $words = [];
    $languages = $content->getTranslationLanguages();

    foreach ($languages as $language) {
      $content = $content->getTranslation($language->getId());
      $document = $content->get('document')->view([]);

      $text = $this->renderer->renderPlain($document);
      foreach ($content->get('search_keywords') as $search_keywords_item) {
        $text .= $search_keywords_item->getString();
      }

      $words += $this->searchIndex->index(
        $this->getPluginId(),
        $content->id(),
        $content->language()->getId(),
        $text,
        FALSE,
      );
    }

    return $words;
  }

  /**
   * {@inheritdoc}
   */
  public function indexClear(): void {
    $this->searchIndex->clear($this->getPluginId());
  }

  /**
   * {@inheritdoc}
   */
  public function markForReindex(): void {
    $this->searchIndex->markForReindex($this->getPluginId());
  }

  /**
   * {@inheritdoc}
   */
  public function indexStatus(): array {
    $total = $this->database->select('druki_content', 'dc')
      ->countQuery()
      ->execute()
      ->fetchField();

    $remaining_query = $this->database->select('druki_content', 'dc');
    $remaining_query->leftJoin('search_dataset', 'sd', '[sd].[sid] = [dc].[internal_id] AND [sd].[type] = :type', [':type' => $this->getPluginId()]);
    $remaining_query->condition(
      $remaining_query->orConditionGroup()
        ->isNull('sd.sid')
        ->condition('sd.reindex', 0, '<>')
    );
    $remaining_query->addExpression('COUNT(DISTINCT [dc].[internal_id])');
    $remaining = $remaining_query->execute()->fetchField();

    return ['total' => $total, 'remaining' => $remaining];
  }

  /**
   * Prepares search results for rendering.
   *
   * @param \Drupal\Core\Database\StatementInterface $found
   *   Found results.
   *
   * @return array
   *   An array with result items.
   *
   * @todo Replace by custom theme hook and buildResults() override.
   */
  protected function prepareResults(StatementInterface $found): array {
    $results = [];
    foreach ($found as $item) {
      $content = $this->contentStorage->load($item->sid)->getTranslation($item->langcode);
      $document = $content->get('document')->view([]);

      $text = $this->renderer->renderPlain($document);
      $results[] = [
        'link' => $content->toUrl('canonical', ['absolute' => TRUE])->toString(),
        'type' => $content->label(),
        'title' => $content->label(),
        'extra' => '123',
        'score' => $item->calculated_score,
        'snippet' => search_excerpt($this->keywords, $text, $item->langcode),
        'langcode' => $item->langcode,
      ];
    }
    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function searchFormAlter(array &$form, FormStateInterface $form_state): void {
    $form = [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildResults() {
    // @todo
    return parent::buildResults();
  }

  /**
   * {@inheritdoc}
   */
  public function suggestedTitle() {
    // @todo
    return \rand(0, 10000);
  }

}
