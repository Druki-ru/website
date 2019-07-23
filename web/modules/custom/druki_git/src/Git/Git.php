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
  public static function getLastCommitId(string $dir): string {
    $process = new Process('git log --format="%H" -n 1', $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    return $process->getOutput();
  }

  /**
   * Gets the file list commit ID.
   *
   * @param string $filepath
   *   The relative path to the file.
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @return string
   *   The command output.
   */
  public static function getFileLastCommitId(string $filepath, string $dir): string {
    $process = new Process('git log --format="%H" -n 1 -- ' . $filepath, $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    return $process->getOutput();
  }

  /**
   * Gets the file list commit ID.
   *
   * @param string $filepath
   *   The relative path to the file.
   * @param string $dir
   *   The absolute path to working dir.
   *
   * @see https://stackoverflow.com/a/43042363/4751623
   *
   * @return array
   *   The array with contribution statistics.
   */
  public static function getFileCommitsInfo(string $filepath, string $dir): array {
    $process = new Process('git shortlog HEAD -sen -- ' . $filepath, $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    $results = explode(PHP_EOL, rtrim($process->getOutput()));
    $commits_info = [];
    foreach ($results as $item) {
      preg_match_all("/(\d+)\s(.+)\s<([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)>/", $item, $matches);

      $commits_info[] = [
        'count' => $matches[1][0],
        'name' => $matches[2][0],
        'email' => $matches[3][0],
      ];
    }

    return $commits_info;
  }

}
