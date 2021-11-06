<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

/**
 * Provides an interface for content webhook settings.
 */
interface ContentWebhookSettingsInterface {

  /**
   * Gets git webhook access key.
   *
   * @return string
   *   The webhook access key.
   */
  public function getContentUpdateWebhookAccessKey(): string;

  /**
   * Sets webhook access key.
   *
   * @param string $access_key
   *   The access key.
   *
   * @return $this
   */
  public function setContentUpdateWebhookAccessKey(string $access_key): self;

}
