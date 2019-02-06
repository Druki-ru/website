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
   * Deletes tracking info for file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   */
  public function delete(FileInterface $file) {
    $this->database->delete('druki_file_tracker')
      ->condition('id', $file->id())
      ->execute();
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
        'id' => $file->id(),
        'file_hash' => $this->getFileHash($file),
      ])
      ->execute();
  }

  /**
   * Gets file hash.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   *
   * @return string
   *   The file hash.
   */
  protected function getFileHash(FileInterface $file) {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $file->getFileUri());

    if (!isset($result)) {
      $result = md5_file($file->getFileUri());
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
        'id' => $file->id(),
        'file_hash' => $this->getFileHash($file),
      ])
      ->execute();
  }

}
