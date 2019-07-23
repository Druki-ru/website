<?php

namespace Drupal\druki_git\Service;

/**
 * Interface GitInterface.
 *
 * @package Drupal\druki_git\Service
 */
interface GitInterface {

  /**
   * Trying to pull actual data from remote repository.
   *
   * @return bool
   *   TRUE if success, FALSE otherwise.
   */
  public function pull(): bool;

  /**
   * Gets latest commit id from local repository.
   *
   * @return string
   *   The commit id.
   */
  public function getLastCommitId(): string;

  /**
   * Gets repository path.
   *
   * @return string|null
   *   The URI to repository.
   */
  public function getRepositoryPath(): ?string;

  /**
   * Gets repository realpath.
   *
   * @return string|null
   *   The realpath to repository.
   */
  public function getRepositoryRealpath(): ?string;

  /**
   * Gets last commit hash for file.
   *
   * @param string $relative_path
   *   Relative path in repository to file.
   *
   * @return string|null
   *   The commit hash id, NULL if not found.
   */
  public function getFileLastCommitId($relative_path): ?string;

  /**
   * Gets file commits statistics.
   *
   * @param string $relative_path
   *   Relative path in repository to file.
   *
   * @return array
   *   An array with contributions. Contains:
   *   - count: Amount of commits for this file byt this contributor.
   *   - name: The username of contributor.
   *   - email: The email of contributor.
   */
  public function getFileCommitsInfo($relative_path): array;

}
