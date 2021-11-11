<?php

declare(strict_types=1);

namespace Drupal\druki_author\Data;

use Drupal\druki\Queue\EntitySyncQueueItemInterface;

/**
 * Provides queue item with AuthorList.
 */
final class AuthorListQueueItem implements EntitySyncQueueItemInterface {

  /**
   * The queue item payload.
   */
  protected AuthorList $payload;

  /**
   * Constructs a new AuthorListQueueItem object.
   *
   * @param \Drupal\druki_author\Data\AuthorList $author_list
   *   The author list.
   */
  public function __construct(AuthorList $author_list) {
    $this->payload = $author_list;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload(): AuthorList {
    return $this->payload;
  }

}
