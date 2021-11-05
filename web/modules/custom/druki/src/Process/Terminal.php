<?php

declare(strict_types=1);

namespace Drupal\druki\Process;

use Symfony\Component\Process\Process;

/**
 * Provides terminal for CLI interaction.
 *
 * This class is wrapper around Symfony's Process component. This is done
 * because code that uses it should be tested, but the Process can't be easily
 * mocked directly. This service should be used to work with that component, in
 * that case it can be easily mocked via this service.
 *
 * @see https://symfony.com/doc/current/components/process.html
 */
final class Terminal implements TerminalInterface {

  /**
   * {@inheritdoc}
   */
  public function createProcess(array $command, string $cwd = NULL, array $env = NULL, $input = NULL, ?float $timeout = 60): Process {
    return new Process($command, $cwd, $env, $input, $timeout);
  }

}
