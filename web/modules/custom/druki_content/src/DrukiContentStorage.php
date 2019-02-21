<?php

namespace Drupal\druki_content;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DrukiContentStorage
 *
 * @package Drupal\druki_content
 */
class DrukiContentStorage extends SqlContentEntityStorage {

  /**
   * The git settings configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $gitSettings;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    Connection $database,
    EntityManagerInterface $entity_manager,
    CacheBackendInterface $cache,
    LanguageManagerInterface $language_manager,
    ConfigFactoryInterface $config_factory,
    MemoryCacheInterface $memory_cache = NULL
  ) {

    parent::__construct($entity_type, $database, $entity_manager, $cache, $language_manager, $memory_cache);

    $this->gitSettings = $config_factory->get('druki_git.git_settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('database'),
      $container->get('entity.manager'),
      $container->get('cache.entity'),
      $container->get('language_manager'),
      $container->get('config.factory'),
      $container->get('entity.memory_cache')
    );
  }

  /**
   * Loads content by its external ID.
   */
  public function loadByMeta($external_id, $langcode = NULL) {
    if (!$langcode) {
      $langcode = $this->languageManager->getCurrentLanguage()->getId();
    }

    $entity_query = $this->getQuery();
    $entity_query->accessCheck(FALSE);
    $entity_query->condition('external_id', $external_id)
      ->condition('langcode', $langcode);

    $result = $entity_query->execute();
    if (!empty($result)) {
      array_shift($result);

      return $this->load($result[0]);
    }

    return NULL;
  }

  /**
   * Delete missing druki_content.
   *
   * This find and delete missing content. Missing content means content which
   * original file is not found in actual repo.
   */
  public function deleteMissing() {
    $repository_path = trim($this->gitSettings->get('repository_path'), '/');
    $entities = $this->doLoadMultiple();

    /** @var \Drupal\druki_content\Entity\DrukiContentInterface $entity */
    foreach ($entities as $entity) {
      $fullpath = $repository_path . '/' . $entity->get('relative_pathname')->value;

      if (!file_exists($fullpath)) {
        $entity->delete();
      }
    }
  }

}
