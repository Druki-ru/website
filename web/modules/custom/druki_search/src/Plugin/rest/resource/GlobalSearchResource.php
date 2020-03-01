<?php

namespace Drupal\druki_search\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ResourceResponseInterface;
use Drupal\search_api\ParseMode\ParseModePluginManager;
use Drupal\search_api\Query\QueryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Represents Global Search records as resources.
 *
 * @RestResource (
 *   id = "druki_search_global",
 *   label = @Translation("Global search"),
 *   uri_paths = {
 *     "canonical" = "/api/search/global",
 *   }
 * )
 *
 * @deprecated remote after 15.02.19 commits. Unused.
 */
class GlobalSearchResource extends ResourceBase {

  /**
   * Current request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Search API Index storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $indexStorage;

  /**
   * Parse mode plugin manager.
   *
   * @var \Drupal\search_api\ParseMode\ParseModePluginManager
   */
  protected $parseMode;

  /**
   * Constructs a Drupal\rest\Plugin\rest\resource\EntityResource object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    Request $request,
    EntityTypeManagerInterface $entity_type_manger,
    ParseModePluginManager $parse_mode
  ) {

    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->request = $request;
    $this->indexStorage = $entity_type_manger->getStorage('search_api_index');
    $this->parseMode = $parse_mode;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.search_api.parse_mode')
    );
  }

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the record.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\search_api\SearchApiException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function get(): ResourceResponseInterface {
    $cache = CacheableMetadata::createFromRenderArray([
      '#cache' => [
        'max-age' => 60 * 60 * 24,
        'contexts' => [
          'url.query_args:text',
          'url.query_args:core',
          'url.query_args:difficulty',
        ],
      ],
    ]);

    $results = [
      'items' => [],
    ];

    $this->doSearch($results, $cache);

    $response = new ResourceResponse($results);
    $response->addCacheableDependency($cache);

    return $response;
  }

  /**
   * Search on site.
   *
   * @param array $results
   *   The results array.
   * @param \Drupal\Core\Cache\CacheableMetadata $cache
   *   The cache metadata.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\search_api\SearchApiException
   */
  public function doSearch(array &$results, CacheableMetadata $cache): void {
    /** @var \Drupal\search_api\IndexInterface $index */
    $index = $this->indexStorage->load('global');
    /** @var \Drupal\search_api\ParseMode\ParseModeInterface $parse_mode */
    $parse_mode = $this->parseMode->createInstance('terms');
    $parse_mode->setConjunction('AND');
    $search_query = $index->query();
    $search_query->setParseMode($parse_mode)
      ->range(0, 50)
      ->sort('search_api_relevance', QueryInterface::SORT_DESC);

    // Handle additional filters.
    if ($this->request->query->has('text')) {
      $keys = [
        '#conjunction' => 'OR',
      ];
      $text = $this->request->query->get('text');
      $keys[] = $text;

      if (!$this->isRussian($text)) {
        $keys[] = $this->ytNfHfcrkflrf($text);
      }

      $search_query->keys($keys);
    }

    if ($this->request->query->has('difficulty')) {
      $difficulty = $this->request->query->get('difficulty');
      $search_query->addCondition('difficulty', $difficulty, 'IN');
    }

    if ($this->request->query->has('core')) {
      $core = $this->request->query->get('core');
      $search_query->addCondition('core', $core, 'IN');
    }

    $query_results = $search_query->execute();

    foreach ($query_results->getResultItems() as $result_item) {
      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      $entity = $result_item->getOriginalObject()->getValue();
      $cache->addCacheableDependency($entity);

      $result = [
        'type' => $entity->getEntityTypeId() . '--' . $entity->bundle(),
        'label' => $entity->label(),
        'url' => $entity
          ->toUrl('canonical', ['absolute' => TRUE])
          ->toString(TRUE)
          ->getGeneratedUrl(),
      ];

      if ($entity instanceof DrukiContentInterface) {
        $result['core'] = $entity->getCore();
      }

      $results['items'][] = $result;
    }
  }

  /**
   * Checks is this string contain russian chars or not.
   *
   * @param string $string
   *   The string to test.
   *
   * @return bool
   *   TRUE if contain russian chars, FALSE otherwise.
   */
  protected function isRussian(string $string): bool {
    return preg_match('/[А-Яа-яЁё]/u', $string);
  }

  /**
   * If string not
   */
  protected function ytNfHfcrkflrf(string $string): string {
    // Anyway this string will be lowered in Search API. This reduce the size of
    // replacement array.
    $string = strtolower($string);

    $replacements = [
      'q' => 'й',
      'w' => 'ц',
      'e' => 'у',
      'r' => 'к',
      't' => 'е',
      'y' => 'н',
      'u' => 'г',
      'i' => 'ш',
      'o' => 'щ',
      'p' => 'з',
      '[' => 'х',
      ']' => 'ъ',
      'a' => 'ф',
      's' => 'ы',
      'd' => 'в',
      'f' => 'а',
      'g' => 'п',
      'h' => 'р',
      'j' => 'о',
      'k' => 'л',
      'l' => 'д',
      ';' => 'ж',
      '\'' => 'э',
      'z' => 'я',
      'x' => 'ч',
      'c' => 'с',
      'v' => 'м',
      'b' => 'и',
      'n' => 'т',
      'm' => 'ь',
      ',' => 'б',
      '.' => 'ю',
    ];

    return strtr($string, $replacements);
  }

}
