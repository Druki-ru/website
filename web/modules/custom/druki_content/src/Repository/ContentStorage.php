<?php

namespace Drupal\druki_content\Repository;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\druki_content\Entity\ContentInterface;

/**
 * Provides storage handler for "druki_content" entity.
 */
final class ContentStorage extends SqlContentEntityStorage {

  /**
   * Loads content by slug.
   *
   * @param string $slug
   *   The content slug.
   * @param string|null $langcode
   *   The langcode of content. Defaults to site language.
   *
   * @return \Drupal\druki_content\Entity\ContentInterface|null
   *   The content.
   */
  public function loadBySlug(string $slug, ?string $langcode = NULL): ?ContentInterface {
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

}
