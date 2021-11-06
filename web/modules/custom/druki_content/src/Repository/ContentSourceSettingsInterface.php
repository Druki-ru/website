<?php

declare(strict_types=1);

namespace Drupal\druki_content\Repository;

/**
 * Provides an interface for content source settings repository.
 */
interface ContentSourceSettingsInterface {

  /**
   * Gets repository local URI.
   *
   * @return string|null
   *   The repository URI.
   */
  public function getRepositoryUri(): ?string;

  /**
   * Sets repository local URI.
   *
   * @param string $path
   *   The repository local URI.
   *
   * @return $this
   */
  public function setRepositoryUri(string $path): self;

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

}
