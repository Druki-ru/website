<?php

namespace Drupal\druki_content\Entity\Handler\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides storage handler for "druki_content" entity.
 */
final class DrukiContentStorage extends SqlContentEntityStorage {

  /**
   * Loads content by slug.
   *
   * @param string $slug
   *   The content slug.
   * @param string|null $langcode
   *   The langcode of content. Defaults to site language.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|null
   *   The content.
   */
  public function loadBySlug(string $slug, ?string $langcode = NULL): ?DrukiContentInterface {
    if (!$langcode) {
      $langcode = $this->languageManager->getCurrentLanguage()->getId();
    }

    $entity_query = $this->getQuery();
    $entity_query->accessCheck(FALSE);
    $and = $entity_query->andConditionGroup();
    $and->condition('slug', $slug);
    $and->condition('langcode', $langcode);
    $entity_query->condition($and);

    $result = $entity_query->execute();
    if (!empty($result)) {
      \reset($result);
      $first = \key($result);
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
