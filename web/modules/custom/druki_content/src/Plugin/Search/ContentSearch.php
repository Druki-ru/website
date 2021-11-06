<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\Search;

use Drupal\Core\Config\Config;
use Drupal\Core\Database\Connection;
use Drupal\druki_content\Repository\DrukiContentStorage;
use Drupal\search\Plugin\SearchIndexingInterface;
use Drupal\search\Plugin\SearchPluginBase;
use Drupal\search\SearchIndexInterface;
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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->database = $container->get('database');
    $instance->searchSettings = $container->get('config.factory')->get('search.settings');
    $instance->searchIndex = $container->get('search.index');
    $instance->contentStorage = $container->get('entity_type.manager')->getStorage('druki_content');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(): array {
    return [];
    if ($this->isSearchExecutable()) {
      $results = $this->findResults();

      if ($results) {
        return $this->prepareResults($results);
      }
    }

    return [];
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
        $words += $this->searchIndex->index(
          $this->getPluginId(),
          $entity->id(),
          $entity->language()->getId(),
          $entity->label(),
          FALSE,
        );
      }
    } finally {
      $this->searchIndex->updateWordWeights($words);
    }
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

}
