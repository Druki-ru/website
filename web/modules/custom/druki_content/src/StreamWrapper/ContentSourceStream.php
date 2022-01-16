<?php

declare(strict_types=1);

namespace Drupal\druki_content\StreamWrapper;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StreamWrapper\StreamWrapperInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a Druki content source (content-source://) stream wrapper.
 *
 * Provides support to accessing files from content source directory without
 * needs of knowing where it's located.
 */
final class ContentSourceStream extends PublicStream {

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
    /** @var \Drupal\druki_content\Repository\ContentSettingsInterface $content_settings */
    $content_settings = \Drupal::service('druki_content.repository.content_settings');
    $repository_uri = $content_settings->getContentSourceUri();
    if ($repository_uri) {
      return $repository_uri;
    }
    return parent::basePath($site_path);
  }

}
