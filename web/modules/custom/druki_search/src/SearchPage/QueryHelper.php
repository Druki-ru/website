<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\search_api\ParseMode\ParseModePluginManager;
use Drupal\search_api\Utility\QueryHelperInterface;

class QueryHelper {

  public const FULL = 'druki_search.search_page.full';

  public const FILTERED = 'druki_search.search_page.filtered';

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

  public function __construct(QueryHelperInterface $query_helper, EntityTypeManagerInterface $entity_type_manager, ParseModePluginManager $parse_mode_plugin_manager) {
    $this->queryHelper = $query_helper;
    $this->indexStorage = $entity_type_manager->getStorage('search_api_index');
    $this->parseModeManager = $parse_mode_plugin_manager;
  }

  public function getQuery($search_id = self::FULL) {
    $index = $this->indexStorage->load('global');
    $query = $this->queryHelper->createQuery($index);
    $query->setParseModeManager($this->parseModeManager);
    $parse_mode = $this->parseModeManager->createInstance('terms');
    $query->setParseMode($parse_mode);
    $query->setSearchId($search_id);

    return $query;
  }

}
