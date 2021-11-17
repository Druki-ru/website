<?php

declare(strict_types=1);

namespace Drupal\druki_author\Queue;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
use Drupal\druki_author\Data\Author;
use Drupal\druki_author\Data\AuthorListQueueItem;

/**
 * Provides author list queue item processor.
 */
final class AuthorListQueueItemProcessor implements EntitySyncQueueItemProcessorInterface {

  /**
   * The author entity storage.
   */
  protected EntityStorageInterface $authorStorage;

  /**
   * Constructs a new AuthorListQueueItemProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->authorStorage = $entity_type_manager->getStorage('druki_author');
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(EntitySyncQueueItemInterface $item): bool {
    return $item instanceof AuthorListQueueItem;
  }

  /**
   * {@inheritdoc}
   */
  public function process(EntitySyncQueueItemInterface $item): array {
    \assert($item instanceof AuthorListQueueItem);
    $ids = [];
    /** @var \Drupal\druki_author\Data\Author $author */
    foreach ($item->getPayload() as $author) {
      $ids[] = $this->processAuthor($author);
    }
    return $ids;
  }

  /**
   * Process single author value object.
   *
   * @param \Drupal\druki_author\Data\Author $author
   *   The author value object.
   *
   * @return string
   *   The author entity ID that was processed or created.
   */
  protected function processAuthor(Author $author): string {
    if ($author_entity = $this->authorStorage->load($author->getId())) {
      // @todo Checksum condition.
      return $author_entity->id();
    }
    else {
      /** @var \Drupal\druki_author\Entity\AuthorInterface $author_entity */
      $author_entity = $this->authorStorage->create();
      $author_entity->setId($author->getId());
    }

    $author_entity->setName(
      $author->getNameGiven(),
      $author->getNameFamily(),
    );
    $author_entity->setCountry($author->getCountry());

    $author_entity->clearOrganization();
    if ($author->getOrgName() && $author->getOrgUnit()) {
      $author_entity->setOrganization($author->getOrgName(), $author->getOrgUnit());
    }

    $author_entity->clearHomepage();
    if ($author->getHomepage()) {
      $author_entity->setHomepage($author->getHomepage());
    }

    $author_entity->clearDescription();
    if ($author->getDescription()) {
      $author_entity->setDescription($author->getDescription());
    }

    $author_entity->clearImage();
    if ($author->getImage()) {
      // @todo Process and download image if necessary.
    }

    $author_entity->save();
    return $author_entity->id();
  }

}
