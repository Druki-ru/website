<?php

namespace Drupal\druki\File;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\file\FileInterface;
use Drupal\file\FileUsage\FileUsageInterface;

/**
 * Provides file tracker implementation.
 */
final class FileTracker implements FileTrackerInterface {

  /**
   * The file storage.
   *
   * @var \Drupal\file\FileStorageInterface|\Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fileStorage;

  /**
   * The logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * The file usage.
   *
   * @var \Drupal\file\FileUsage\FileUsageInterface
   */
  protected $fileUsage;

  /**
   * The media storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaStorage;

  /**
   * DrukiFileTracker constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger.
   * @param \Drupal\file\FileUsage\FileUsageInterface $file_usage
   *   The file usage.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelInterface $logger,
    FileUsageInterface $file_usage
  ) {

    $this->fileStorage = $entity_type_manager->getStorage('file');
    $this->logger = $logger;
    $this->fileUsage = $file_usage;
    $this->mediaStorage = $entity_type_manager->getStorage('media');
  }

  /**
   * {@inheritdoc}
   */
  public function checkDuplicate(string $uri): ?EntityInterface {
    $file_hash = $this->getFileHash($uri);

    $result = $this
      ->fileStorage
      ->getQuery()
      ->condition('druki_file_hash', $file_hash)
      ->range(0, 1)
      ->sort('fid')
      ->execute();

    if (!empty($result)) {
      $fid = \reset($result);

      return $this->fileStorage->load($fid);
    }

    return NULL;
  }

  /**
   * Gets file hash.
   *
   * @param string $uri
   *   The URI to file.
   *
   * @return string
   *   The file hash.
   */
  protected function getFileHash(string $uri): string {
    $result = &drupal_static(self::class . ':' . __METHOD__ . ':' . $uri);

    if (!isset($result)) {
      $result = \md5_file($uri);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function updateTrackingInformation(): void {
    $this->clearTrackingInformation();

    $file_ids = $this
      ->fileStorage
      ->getQuery()
      ->exists('uri')
      ->condition('status', \FILE_STATUS_PERMANENT)
      ->execute();

    $files = $this->fileStorage->loadMultiple($file_ids);

    /** @var \Drupal\file\FileInterface $file */
    foreach ($files as $file) {
      $this->track($file);
    }
  }

  /**
   * Deletes all tracking information.
   */
  protected function clearTrackingInformation(): void {
    $files = $this->fileStorage->loadMultiple();

    /** @var \Drupal\file\FileInterface $file */
    foreach ($files as $file) {
      $file->set('druki_file_hash', NULL);
      $file->save();
    }

    $this->logger->notice('The file tracking information is cleared.');
  }

  /**
   * {@inheritdoc}
   */
  public function track(FileInterface $file): bool {
    if ($file->isPermanent()) {
      $file_hash = $this->getFileHash($file->getFileUri());
      $file->set('druki_file_hash', $file_hash);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getMediaForFile(FileInterface $file): ?EntityInterface {
    $usage = $this->fileUsage->listUsage($file);
    // Since there is possible to have multiple usage of the same file in
    // different media entities through code and other modules, we just pick the
    // first one.
    $media_id = isset($usage['file']['media']) ? \array_keys($usage['file']['media'])[0] : NULL;
    if ($media_id) {
      return $this->mediaStorage->load($media_id);
    }

    return NULL;
  }

}
