<?php

namespace Drupal\druki_file\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\file\FileInterface;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\media\MediaInterface;

/**
 * Class DrukiFileTracker
 *
 * @package Drupal\druki_file\Service
 */
class DrukiFileTracker {

  /**
   * The file storage.
   *
   * @var \Drupal\file\FileStorageInterface
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
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger factory.
   * @param \Drupal\file\FileUsage\FileUsageInterface $file_usage
   *   The file usage.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger,
    FileUsageInterface $file_usage
  ) {

    $this->fileStorage = $entity_type_manager->getStorage('file');
    $this->logger = $logger->get('druki_file');
    $this->fileUsage = $file_usage;
    $this->mediaStorage = $entity_type_manager->getStorage('media');
  }

  /**
   * Updates tracking information about file or creates new one if this is new.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   *
   * @return bool
   *   TRUE if tracking was successful, FALSE if file is not permanent.
   *
   * @throws \Exception
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
   * Gets file hash.
   *
   * @param string $uri
   *   The URI to file.
   *
   * @return string
   *   The file hash.
   */
  protected function getFileHash($uri): string {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $uri);

    if (!isset($result)) {
      $result = md5_file($uri);
    }

    return $result;
  }

  /**
   * Checks if file from provided uri is duplicate on of the existed.
   *
   * @param string $uri
   *   The URI to file, need to be checked.
   *
   * @return \Drupal\file\FileInterface|null
   *   The file entity, which store the same file, NULL if not found.
   */
  public function checkDuplicate(string $uri): ?FileInterface {
    $file_hash = $this->getFileHash($uri);

    $result = $this
      ->fileStorage
      ->getQuery()
      ->condition('druki_file_hash', $file_hash)
      ->range(0, 1)
      ->execute();

    if (!empty($result)) {
      $fid = reset($result);

      return $this->fileStorage->load($fid);
    }

    return NULL;
  }

  /**
   * Loads all files and add\update tracking information for them.
   */
  public function updateTrackingInformation(): void {
    $this->clearTrackingInformation();

    $file_ids = $this
      ->fileStorage
      ->getQuery()
      ->exists('uri')
      ->condition('status', FILE_STATUS_PERMANENT)
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

    /** @var FileInterface $file */
    foreach ($files as $file) {
      $file->set('druki_file_hash', NULL);
      $file->save();
    }
  }

  /**
   * Looking for media entity that uses file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   *
   * @return \Drupal\media\MediaInterface|null
   *   The media entity, if found, NULL otherwise.
   */
  public function getMediaForFile(FileInterface $file): ?MediaInterface {
    $usage = $this->fileUsage->listUsage($file);
    // Since there is possible to have multiple usage of the same file in
    // different media entities through code and other modules, we just pick the
    // first one.
    $media_id = (isset($usage['file']['media'])) ? array_keys($usage['file']['media'])[0] : NULL;
    if ($media_id) {
      return $this->mediaStorage->load($media_id);
    }

    return NULL;
  }

}
