<?php

namespace Drupal\druki\File;

use Drupal\Core\Entity\EntityInterface;
use Drupal\file\FileInterface;

/**
 * Provides file tracker.
 */
interface FileTrackerInterface {

  /**
   * Checks if file from provided uri is duplicate on of the existed.
   *
   * @param string $uri
   *   The URI to file, need to be checked.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The file entity, which store the same file, NULL if not found.
   */
  public function checkDuplicate(string $uri): ?EntityInterface;

  /**
   * Loads all files and add\update tracking information for them.
   */
  public function updateTrackingInformation(): void;

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
  public function track(FileInterface $file): bool;

  /**
   * Looking for media entity that uses file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The media entity, if found, NULL otherwise.
   */
  public function getMediaForFile(FileInterface $file): ?EntityInterface;

}
