<?php

namespace Drupal\druki_file\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\file\FileInterface;

/**
 * Class DrukiFileTracker
 *
 * @package Drupal\druki_file\Service
 */
class DrukiFileTracker {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

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
   * DrukiFileTracker constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   The logger factory.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    Connection $database,
    EntityTypeManagerInterface $entity_type_manager,
    LoggerChannelFactoryInterface $logger
  ) {

    $this->database = $database;
    $this->fileStorage = $entity_type_manager->getStorage('file');
    $this->logger = $logger->get('druki_file');
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
  public function track(FileInterface $file) {
    if ($file->isPermanent()) {
      if ($this->isFileTracked($file)) {
        $this->update($file);
      }
      else {
        $this->create($file);
      }

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Checks is file has record in database.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   *
   * @return bool
   *   TRUE if tracker, FALSE otherwise.
   */
  public function isFileTracked(FileInterface $file) {
    return (bool) $this->database->select('druki_file_tracker', 'ft')
      ->condition('ft.fid', $file->id())
      ->countQuery()
      ->execute()
      ->fetchField();
  }

  /**
   * Updates record for file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   */
  protected function update(FileInterface $file) {
    $this->database->update('druki_file_tracker')
      ->fields([
        'fid' => $file->id(),
        'file_hash' => $this->getFileHash($file->getFileUri()),
      ])
      ->execute();
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
  protected function getFileHash($uri) {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $uri);

    if (!isset($result)) {
      $result = md5_file($uri);
    }

    return $result;
  }

  /**
   * Creates new record for file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   *
   * @throws \Exception
   */
  protected function create(FileInterface $file) {
    $this->database->insert('druki_file_tracker')
      ->fields([
        'fid' => $file->id(),
        'file_hash' => $this->getFileHash($file->getFileUri()),
      ])
      ->execute();
  }

  /**
   * Deletes tracking info for file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   */
  public function delete(FileInterface $file) {
    $this->database->delete('druki_file_tracker')
      ->condition('fid', $file->id())
      ->execute();
  }

  /**
   * Checks if file from provided uri is duplicate on of the existed.
   *
   * @param string $uri
   *   The URI to file, need to be checked.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The file entity, which store the same file, NULL if not found.
   */
  public function checkDuplicate($uri) {
    $file_hash = $this->getFileHash($uri);
    $result = $this->database->select('druki_file_tracker', 'ft')
      ->fields('ft', ['fid'])
      ->condition('ft.file_hash', $file_hash)
      ->execute()
      ->fetchField();

    if (is_numeric($result)) {
      return $this->fileStorage->load($result);
    }
  }

  /**
   * Loads all files and add\update tracking information for them.
   */
  public function updateTrackingInformation() {
    $this->clearTrackingInformation();

    $file_ids = $this->fileStorage->getQuery()
      ->exists('uri')
      ->condition('status', FILE_STATUS_PERMANENT)
      ->execute();

    $files = $this->fileStorage->loadMultiple($file_ids);

    /** @var \Drupal\file\FileInterface $file */
    foreach ($files as $file) {
      $this->create($file);
    }
  }

  /**
   * Deletes all tracking information.
   */
  protected function clearTrackingInformation() {
    $this->database->delete('druki_file_tracker')->execute();
  }

}
