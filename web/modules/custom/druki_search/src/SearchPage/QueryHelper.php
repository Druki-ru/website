<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\ParseMode\ParseModePluginManager;
use Drupal\search_api\Utility\QueryHelperInterface;

/**
 * Provides query helper for global search.
 *
 * @todo delete. Don't need it for now.
 */
class QueryHelper {

  /**
   * Marks query as full search, without being filtered.
   */
  const FULL = 'druki_search.search_page.full';

  /**
   * The query tipe which can be filtered not directly by others.
   */
  const FILTERED = 'druki_search.search_page.filtered';

  /**
   * The query helper.
   *
   * @var \Drupal\search_api\Utility\QueryHelperInterface
   */
  protected $queryHelper;

  /**
   * The parse mode plugin manager.
   *
   * @var \Drupal\search_api\ParseMode\ParseModePluginManager
   */
  protected $parseModeManager;

  /**
   * The index storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $indexStorage;

  /**
   * Constructs a new QueryHelper object.
   *
   * @param \Drupal\search_api\Utility\QueryHelperInterface $query_helper
   *   The search api query helper.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\search_api\ParseMode\ParseModePluginManager $parse_mode_plugin_manager
   *   The parse mode plugin manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(QueryHelperInterface $query_helper, EntityTypeManagerInterface $entity_type_manager, ParseModePluginManager $parse_mode_plugin_manager) {
    $this->queryHelper = $query_helper;
    $this->indexStorage = $entity_type_manager->getStorage('search_api_index');
    $this->parseModeManager = $parse_mode_plugin_manager;
  }

  /**
   * Gets instance of query.
   *
   * @param string $search_id
   *   The search id.
   *
   * @return \Drupal\search_api\Query\QueryInterface
   *   The query.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function getQuery($search_id = self::FULL) {
    $index = $this->indexStorage->load('global');
    /** @var \Drupal\search_api\Query\Query $query */
    $query = $this->queryHelper->createQuery($index);
    $query->setParseModeManager($this->parseModeManager);
    $parse_mode = $this->parseModeManager->createInstance('terms');
    $query->setParseMode($parse_mode);
    $query->setSearchId($search_id);

    return $query;
  }

}
