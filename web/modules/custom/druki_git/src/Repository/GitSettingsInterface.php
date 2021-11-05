<?php

namespace Drupal\druki_git\Repository;

/**
 * Provides interface for git settings implementations.
 */
interface GitSettingsInterface {

  /**
   * Gets repository local path.
   *
   * @return string|null
   *   The repository URI.
   */
  public function getRepositoryPath(): ?string;

  /**
   * Sets repository local path.
   *
   * @param string $path
   *   The repository local URI.
   *
   * @return $this
   */
  public function setRepositoryPath(string $path): self;

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
   * @return string
   *   The webhook access key.
   */
  public function getWebhookAccessKey(): string;

  /**
   * Sets webhook access key.
   *
   * @param string $key
   *   The access key.
   *
   * @return $this
   */
  public function setWebhookAccessKey(string $key): self;

}
