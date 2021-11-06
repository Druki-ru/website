<?php

declare(strict_types=1);

namespace Drupal\druki\Process;

use Symfony\Component\Process\Process;

/**
 * Provides interface for git interaction classes.
 */
interface GitInterface {

  /**
   * Calls 'git pull' in provided directory.
   *
   * @param string $directory
   *   The working repository directory.
   *
   * @return \Symfony\Component\Process\Process
   *   The created process.
   */
  public function pull(string $directory): Process;

  /**
   * Gets the last commit ID in repository.
   *
   * @param string $directory
   *   The working repository directory.
   *
   * @return \Symfony\Component\Process\Process
   *   The created process.
   */
  public function getLastCommitId(string $directory): Process;

  /**
   * Gets the last commit ID for specific file in repository.
   *
   * @param string $directory
   *   The working repository directory.
   * @param string $filepath
   *   The relative filepath.
   *
   * @return \Symfony\Component\Process\Process
   *   The crated process.
   */
  public function getFileLastCommitId(string $directory, string $filepath): Process;

  /**
   * Gets the contributors of specific file in repository.
   *
   * @param string $directory
   *   The working repository directory.
   * @param string $filepath
   *   The relative filepath.
   *
   * @return \Symfony\Component\Process\Process
   *   The crated process.
   */
  public function getFileContributors(string $directory, string $filepath): Process;

}
