<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

/**
 * Provides an interface for content entity settings storage.
 */
interface ContentSettingsInterface {

  /**
   * Gets repository local URI.
   *
   * @return string|null
   *   The repository URI.
   */
  public function getContentSourceUri(): ?string;

  /**
   * Sets repository local URI.
   *
   * @param string $uri
   *   The repository local URI.
   *
   * @return $this
   */
  public function setContentSourceUri(string $uri): self;

  /**
   * Gets repository public URL.
   *
   * @return string|null
   *   The repository URL.
   */
  public function getRepositoryUrl(): ?string;

  /**
   * Sets remote repository URL.
   *
   * @param string $url
   *   The remote repository URL.
   *
   * @return $this
   */
  public function setRepositoryUrl(string $url): self;

  /**
   * Gets git webhook access key.
   *
   * @return string|null
   *   The webhook access key.
   */
  public function getContentUpdateWebhookAccessKey(): ?string;

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
