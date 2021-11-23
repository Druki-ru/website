<?php

declare(strict_types=1);

namespace Drupal\druki\Repository;

use Drupal\media\MediaInterface;

/**
 * Defines interface for media image repositories.
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
interface MediaImageRepositoryInterface {

  /**
   * Saves image file by URI.
   *
   * @param string $file_uri
   *   The file URI.
   * @param string $image_alt
   *   The image alt.
   *
   * @return \Drupal\media\MediaInterface|null
   *   The media entity if created. NULL if problem happened.
   */
  public function saveByUri(string $file_uri, string $image_alt): ?MediaInterface;

  /**
   * Loads media file by file URI.
   *
   * @param string $file_uri
   *   The file URI.
   *
   * @return \Drupal\media\MediaInterface|null
   *   The media entity. NULL if not found.
   */
  public function loadByUri(string $file_uri): ?MediaInterface;

}
