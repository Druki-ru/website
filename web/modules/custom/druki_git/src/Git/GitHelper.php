<?php

namespace Drupal\druki_git\Git;

use Drupal\druki_git\Exception\GitCommandFailedException;
use Symfony\Component\Process\Process;

/**
 * Provides git helper implementation.
 */
final class GitHelper implements GitHelperInterface {

  /**
   * {@inheritdoc}
   */
  public static function pull(string $dir): string {
    $process = new Process(['git', 'pull'], $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    return $process->getOutput();
  }

  /**
   * {@inheritdoc}
   */
  public static function getLastCommitId(string $dir): string {
    $process = new Process(['git', 'log', '--format="%H"', '-n 1'], $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    return $process->getOutput();
  }

  /**
   * {@inheritdoc}
   */
  public static function getFileLastCommitId(string $filepath, string $dir): string {
    $process = new Process([
      'git',
      'log',
      '--format="%H"',
      '-n 1',
      '--',
      $filepath,
    ], $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    return $process->getOutput();
  }

  /**
   * {@inheritdoc}
   */
  public static function getFileCommitsInfo(string $filepath, string $dir): array {
    $process = new Process([
      'git',
      'shortlog',
      'HEAD',
      '-sen',
      '--',
      $filepath,
    ], $dir);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new GitCommandFailedException($process);
    }

    $results = \explode(\PHP_EOL, \rtrim($process->getOutput()));
    $commits_info = [];
    foreach ($results as $item) {
      \preg_match_all("/(\d+)\s(.+)\s<([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)>/", $item, $matches);

      $commits_info[] = [
        'count' => $matches[1][0],
        'name' => $matches[2][0],
        'email' => $matches[3][0],
      ];
    }

    return $commits_info;
  }

}
