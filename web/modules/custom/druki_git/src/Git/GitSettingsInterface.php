<?php

namespace Drupal\druki_git\Git;

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
   * Gets repository public URL.
   *
   * @return string|null
   *   The repository URL.
   */
  public function getRepositoryUrl(): ?string;

}
