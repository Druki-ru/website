<?php

declare(strict_types=1);

namespace Drupal\druki_content\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Provides an event for requesting source content synchronization.
 */
final class ContentSourceSyncRequestEvent extends Event {

  /**
   * The source content URI.
   */
  protected string $sourceContentUri;

  /**
   * Constructs a new ContentSourceSyncRequestEvent object.
   *
   * @param string $source_content_uri
   *   The source content URI.
   */
  public function __construct(string $source_content_uri) {
    $this->sourceContentUri = $source_content_uri;
  }

  /**
   * Gets source content URI.
   *
   * @return string
   *   The source content URI.
   */
  public function getSourceContentUri(): string {
    return $this->sourceContentUri;
  }

}
