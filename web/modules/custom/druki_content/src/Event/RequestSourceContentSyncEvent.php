<?php

declare(strict_types=1);

namespace Drupal\druki_content\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Provides an event for requesting source content synchronization.
 *
 * @todo Pass 'druki_content.repository.content_source_settings' with event.
 */
final class RequestSourceContentSyncEvent extends Event {

}
