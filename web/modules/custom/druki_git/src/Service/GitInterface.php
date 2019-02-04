<?php

namespace Drupal\druki_git\Service;

/**
 * Interface GitInterface
 *
 * @package Drupal\druki_git\Service
 */
interface GitInterface {

  /**
   * Trying to access repository by path.
   *
   * @return \Drupal\druki_git\Service\GitInterface|null
   *   The current instance or NULL, if repository not found.
   */
  public function init();

  /**
   * Trying to pull actual data from remote repository.
   *
   * @return \Drupal\druki_git\Service\GitInterface|null
   *   The current instance or NULL, if repository not found.
   */
  public function pull();

  /**
   * Gets latest commit id from local repository.
   *
   * @return string
   *   The commit id.
   */
  public function getLastCommitId();

  /**
   * Gets repository path.
   *
   * @return string|null
   *   The URI to repository.
   */
  public function getRepositoryPath();

  /**
   * Gets repository realpath.
   *
   * @return string|null
   *   The realpath to repository.
   */
  public function getRepositoryRealpath();

  /**
   * Gets last commit hash for file.
   *
   * @param string $relative_path
   *   Relative path in repository to file.
   *
   * @return string|null
   *   The commit hash id, NULL if not found.
   */
  public function getFileLastCommitId($relative_path);

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
  public function getFileCommitsInfo($relative_path);

}
