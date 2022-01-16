<?php

namespace Drupal\druki_content\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\Repository\ContentSettingsInterface;
use Symfony\Component\Routing\Route;

/**
 * The access check for content webhooks.
 */
class ContentWebhookAccess implements AccessInterface {

  /**
   * The content settings.
   */
  protected ContentSettingsInterface $contentSettings;

  /**
   * The state storage.
   */
  protected StateInterface $state;

  /**
   * Constructs a new ContentWebhookAccess object.
   *
   * @param \Drupal\druki_content\Repository\ContentSettingsInterface $content_settings
   *   The content settings.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
   */
  public function __construct(ContentSettingsInterface $content_settings, StateInterface $state) {
    $this->contentSettings = $content_settings;
    $this->state = $state;
  }

  /**
   * Checks for webhook access.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The current route.
   * @param string $access_key
   *   The provided access key.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(Route $route, string $access_key): AccessResultInterface {
    if ($this->state->get('system.maintenance_mode')) {
      return AccessResult::forbidden()->setCacheMaxAge(0);
    }

    $webhook_type = $route->getRequirement('_druki_content_webhook_access_key');
    $expected_access_key = NULL;
    if ($webhook_type == 'content_update') {
      $expected_access_key = $this->contentSettings->getContentUpdateWebhookAccessKey();
    }

    if (!$expected_access_key) {
      return AccessResult::forbidden()->setCacheMaxAge(0);
    }

    if ($access_key == $expected_access_key) {
      return AccessResult::allowed()->setCacheMaxAge(0);
    }

    return AccessResult::forbidden()->setCacheMaxAge(0);
  }

}
