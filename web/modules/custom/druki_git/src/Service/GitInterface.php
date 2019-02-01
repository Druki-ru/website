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

}
