<?php

declare(strict_types=1);

namespace Drupal\druki\Process;

use Drupal\Core\Site\Settings;
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
   * The git binary path.
   */
  protected string $gitBinary;

  /**
   * Constructs a new Git object.
   *
   * @param \Drupal\druki\Process\TerminalInterface $terminal
   *   The terminal process.
   */
  public function __construct(TerminalInterface $terminal) {
    $this->terminal = $terminal;
    $this->gitBinary = Settings::get('druki_git_binary', 'git');
  }

  /**
   * {@inheritdoc}
   */
  public function pull(string $directory): Process {
    // @see https://stackoverflow.com/a/62653400/4751623
    $command = [$this->gitBinary, 'pull', '--ff-only'];
    return $this->terminal->createProcess($command, $directory);
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCommitId(string $directory): Process {
    $command = [$this->gitBinary, 'log', '--format="%H"', '-n 1'];
    return $this->terminal->createProcess($command, $directory);
  }

  /**
   * {@inheritdoc}
   */
  public function getFileLastCommitId(string $directory, string $filepath): Process {
    $command = [
      $this->gitBinary,
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
      $this->gitBinary,
      'shortlog',
      '--summary',
      '--email',
      '--numbered',
      '--',
      $filepath,
    ];
    return $this->terminal->createProcess($command, $directory);
  }

}
