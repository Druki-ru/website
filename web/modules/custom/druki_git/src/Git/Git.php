<?php

namespace Drupal\druki_git\Git;

use Drupal\druki_git\Exception\GitCommandFailedException;
use Symfony\Component\Process\Process;

/**
 * Provide git utility.
 */
class Git {

  /**
   * Pulls from remote repository.
   *
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @return string
   *   The command output.
   */
  public static function pull(string $dir): string {
    $process = new Process('git pull', $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    // @todo event dispatcher.

    return $process->getOutput();
  }

  /**
   * Gets last commit ID (hash).
   *
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @return string
   *   The command output.
   */
  public static function getLastCommitId(string $dir) {
    $process = new Process('git log --format="%H" -n 1', $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    return $process->getOutput();
  }

}
