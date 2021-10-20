<?php

declare(strict_types=1);

namespace Drupal\druki_content\StreamWrapper;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a Druki content source (druki-content-source://) stream wrapper.
 *
 * Provides support to accessing files from content source directory without
 * needs of knowing where it's located.
 */
final class DrukiContentSourceStream extends PublicStream {

  /**
   * {@inheritdoc}
   */
  public static function getType(): int {
    // The source content never should be modified from website, its only
    // allows to read it's content.
    return StreamWrapperInterface::READ_VISIBLE;
  }

  /**
   * {@inheritdoc}
   */
  public function getName(): string {
    return (string) new TranslatableMarkup('Druki content source files');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): string {
    return (string) new TranslatableMarkup('Druki content source files received from git or any other way.');
  }

  /**
   * {@inheritdoc}
   */
  public static function basePath($site_path = NULL): string {
    $repository_path = \Drupal::config('druki_git.git_settings')
      ->get('repository_path');
    if ($repository_path) {
      return $repository_path;
    }
    return parent::basePath($site_path);
  }

}
