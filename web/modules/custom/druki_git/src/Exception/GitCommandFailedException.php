<?php

namespace Drupal\druki_git\Exception;

use Drupal\Component\Render\FormattableMarkup;
use Symfony\Component\Process\Process;

/**
 * Thrown when git command is failed or terminated.
 */
class GitCommandFailedException extends \RuntimeException {

  /**
   * The command process.
   *
   * @var \Symfony\Component\Process\Process
   */
  protected $process;

  /**
   * Constructs a new GitCommandFailedException object.
   *
   * @param \Symfony\Component\Process\Process $process
   *   The command process.
   */
  public function __construct(Process $process) {
    $this->process = $process;
    $this->message = new FormattableMarkup('The git command ":command" failed.', [
      ':command' => $process->getCommandLine(),
    ]);
  }

  /**
   * Gets process for command.
   *
   * @return \Symfony\Component\Process\Process
   *   The command process.
   */
  public function getProcess(): Process {
    return $this->process;
  }

}
