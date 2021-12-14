<?php

declare(strict_types=1);

namespace Drupal\druki\Process;

use Symfony\Component\Process\Process;

/**
 * Provides object to work with Git repositories.
 */
final class Git implements GitInterface {

  /**
   * The terminal process.
   */
  protected TerminalInterface $terminal;

  /**
   * Constructs a new Git object.
   *
   * @param \Drupal\druki\Process\TerminalInterface $terminal
   *   The terminal process.
   */
  public function __construct(TerminalInterface $terminal) {
    $this->terminal = $terminal;
  }

  /**
   * {@inheritdoc}
   */
  public function pull(string $directory): Process {
    // @see https://stackoverflow.com/a/62653400/4751623
    $command = ['git', 'pull', '--ff-only'];
    return $this->terminal->createProcess($command, $directory);
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCommitId(string $directory): Process {
    $command = ['git', 'log', '--format="%H"', '-n 1'];
    return $this->terminal->createProcess($command, $directory);
  }

  /**
   * {@inheritdoc}
   */
  public function getFileLastCommitId(string $directory, string $filepath): Process {
    $command = [
      'git',
      'log',
      '--format="%H"',
      '-n 1',
      '--',
      $filepath,
    ];
    return $this->terminal->createProcess($command, $directory);
  }

  /**
   * {@inheritdoc}
   */
  public function getFileContributors(string $directory, string $filepath): Process {
    $command = [
      'git',
      'shortlog',
      'HEAD',
      '--summary',
      '--email',
      '--numbered',
      '--',
      $filepath,
    ];
    return $this->terminal->createProcess($command, $directory);
  }

}
