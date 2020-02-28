<?php

namespace Drupal\druki_content\Sync;

use Drupal\Component\Render\FormattableMarkup;
use InvalidArgumentException;

/**
 * Provides exception when queue item operation is not allowed.
 */
final class InvalidSyncQueueOperationException extends InvalidArgumentException {

  /**
   * Constructs a new InvalidSyncQueueOperationException object.
   *
   * @param string $operation
   *   The provided operation.
   * @param array $allowed
   *   The allowed values.
   */
  public function __construct(string $operation, array $allowed) {
    $message = new FormattableMarkup('You provided the wrong type of operation @operation. The following values are available: @allowed.', [
      '@operation' => $operation,
      '@allowed' => implode(', ', $allowed),
    ]);
    parent::__construct($message);
  }

}
