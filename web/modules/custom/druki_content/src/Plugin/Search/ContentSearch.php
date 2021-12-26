<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\Search;

use Drupal\Core\Config\Config;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\PagerSelectExtender;
use Drupal\Core\Database\StatementInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki\Utility\Anchor;
use Drupal\druki_content\Builder\ContentTableOfContentsBuilder;
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
        ->condition('sd.reindex', 0, '<>'),
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
        ->condition('sd.reindex', 0, '<>'),
    );
    $remaining_query->addExpression('COUNT(DISTINCT [dc].[internal_id])');
    $remaining = $remaining_query->execute()->fetchField();

    return ['total' => $total, 'remaining' => $remaining];
  }

  /**
   * {@inheritdoc}
   */
  public function buildResults(): array {
    $found = $this->execute();

    $built = [];
    foreach ($found as $item) {
      /** @var \Drupal\druki_content\Entity\DrukiContentInterface $content */
      $content = $this->contentStorage->load($item->sid);
      if (!$content) {
        continue;
      }
      $content = $content->getTranslation($item->langcode);
      $document = $content->get('document')->view([]);

      $text = $this->renderer->renderPlain($document);
      $result = [
        '#theme' => 'druki_search_result',
        '#link' => $content->toUrl()->setAbsolute()->toString(),
        '#title' => $content->label(),
        '#snippet' => \search_excerpt($this->keywords, $text, $item->langcode),
        '#drupal_core' => $content->getCore(),
      ];

      $content_document = $content->getContentDocument();
      $structured_content = $content_document->getContent();
      $toc = ContentTableOfContentsBuilder::build($structured_content);
      if ($toc->getIterator()->count()) {
        $added_headings = 0;
        $max_headings = 3;
        /** @var \Drupal\druki\Data\TableOfContentsHeading $toc_heading */
        foreach ($toc as $toc_heading) {
          if ($added_headings >= $max_headings) {
            break;
          }
          if ($toc_heading->getLevel() != 2) {
            continue;
          }
          $result['#toc'][] = [
            'title' => $toc_heading->getText(),
            'link' => $content->toUrl(options: [
              'absolute' => TRUE,
              'fragment' => Anchor::generate($toc_heading->getText(), 'druki_content', Anchor::REUSE),
            ])->toString(),
          ];
          $added_headings++;
        }
      }

      $built[] = $result;
    }

    return $built;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(): array {
    if ($this->isSearchExecutable()) {
      $results = $this->findResults();
      if ($results) {
        return $results->fetchAll();
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
  public function suggestedTitle(): string {
    if ($this->isSearchExecutable()) {
      return (string) new TranslatableMarkup('Search results for «:keys»', [
        ':keys' => $this->getKeywords(),
      ]);
    }
    return (string) new TranslatableMarkup('Search');
  }

}
