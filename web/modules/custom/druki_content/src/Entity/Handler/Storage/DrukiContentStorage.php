<?php

namespace Drupal\druki_content\Entity\Handler\Storage;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides storage handler for "druki_content" entity.
 */
final class DrukiContentStorage extends SqlContentEntityStorage {

  /**
   * The git settings configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $gitSettings;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $config_factory = $container->get('config.factory');
    $instance->gitSettings = $config_factory->get('druki_git.git_settings');
    return $instance;
  }

  /**
   * Loads content by its meta information.
   *
   * @param string $external_id
   *   The external content ID.
   * @param null|string $langcode
   *   The langcode of content. By default default site language.
   * @param null|string $core
   *   The core version, if applicable.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|null
   *   The content.
   */
  public function loadByExternalId(string $external_id, string $langcode = NULL, string $core = NULL): ?DrukiContentInterface {
    if (!$langcode) {
      $langcode = $this->languageManager->getCurrentLanguage()->getId();
    }

    $entity_query = $this->getQuery();
    $entity_query->accessCheck(FALSE);
    $and = $entity_query->andConditionGroup();
    $and->condition('external_id', $external_id);
    $and->condition('langcode', $langcode);
    if ($core) {
      $and->condition('core', $core);
    }
    else {
      $and->notExists('core');
    }
    $entity_query->condition($and);

    $result = $entity_query->execute();
    if (!empty($result)) {
      reset($result);
      $first = key($result);

      return $this->load($first);
    }

    return NULL;
  }

  /**
   * Clean outdated content.
   *
   * The "outdated" content is the one which "sync_timestamp" value is lower
   * than last sync was complete. This means this content was not presented in
   * any way and should be removed.
   *
   * @param string $timestamp
   *   The last sync timestamp.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function cleanOutdated(string $timestamp): void {
    $ids = $this
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('sync_timestamp', $timestamp, '<')
      ->execute();

    $entities = $this->loadMultiple($ids);
    $this->delete($entities);
  }

}
