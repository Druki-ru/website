<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

use Drupal\druki\File\FileTrackerInterface;
use Drupal\media\MediaInterface;

/**
 * Provides storage for media image storage from the content.
 *
 * The images in source content can be external and local files. No matter what
 * type is used, we should store them with website, even if source files is
 * located in content source directory.
 *
 * This allows us to manipulate with image, making it responsive or multiple
 * sizes with zoom libraries. Also this allows to reduce duplicates files,
 * because it is possible that same file is used in multiple places, but we
 * don't want to store multiple copies of the same file. This repository also
 * cary about finding already existed duplicates and re-used them.
 */
final class ContentMediaImageRepository {

  /**
   * The file tracker.
   */
  protected FileTrackerInterface $fileTracker;

  /**
   * Constructs a new ContentMediaImageRepository object.
   *
   * @param \Drupal\druki\File\FileTrackerInterface $file_tracker
   *   The file tracker.
   */
  public function __construct(FileTrackerInterface $file_tracker) {
    $this->fileTracker = $file_tracker;
  }

  /**
   * Loads media file by file URI.
   *
   * @param string $file_uri
   *   The file URI.
   *
   * @return \Drupal\media\MediaInterface|null
   *   The media entity. NULL if some problem happens.
   */
  public function loadByUri(string $file_uri): ?MediaInterface {
    // @todo Complete this method.
  }

}
