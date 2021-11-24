<?php

declare(strict_types=1);

namespace Drupal\druki_author\Queue;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki\Queue\EntitySyncQueueItemInterface;
use Drupal\druki\Queue\EntitySyncQueueItemProcessorInterface;
use Drupal\druki\Repository\MediaImageRepositoryInterface;
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
   * The media image repository.
   */
  protected MediaImageRepositoryInterface $mediaImageRepository;

  /**
   * Constructs a new AuthorListQueueItemProcessor object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\druki\Repository\MediaImageRepositoryInterface $media_image_repository
   *   The media image repository.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MediaImageRepositoryInterface $media_image_repository) {
    $this->authorStorage = $entity_type_manager->getStorage('druki_author');
    $this->mediaImageRepository = $media_image_repository;
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
      if ($author->checksum() == $author_entity->getChecksum()) {
        return $author_entity->id();
      }
    }
    else {
      /** @var \Drupal\druki_author\Entity\AuthorInterface $author_entity */
      $author_entity = $this->authorStorage->create();
      $author_entity->setId($author->getId());
    }

    $author_entity->setChecksum($author->checksum());

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
      $alt = (string) new TranslatableMarkup('Avatar for author @username', [
        '@username' => $author->getId(),
      ]);
      $author_image_media = $this->mediaImageRepository->saveByUri($author->getImage(), $alt);
      if ($author_image_media) {
        $author_entity->setImageMedia($author_image_media);
      }
    }

    $author_entity->save();
    return $author_entity->id();
  }

}
