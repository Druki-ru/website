<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Process;

use Drupal\druki\Process\Terminal;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Process\Process;

/**
 * Provides test for terminal wrapper.
 *
 * @coversDefaultClass \Drupal\druki\Process\Terminal
 */
final class TerminalTest extends UnitTestCase {

  /**
   * Tests that object is properly creates real Process instance.
   */
  public function testObject(): void {
    $terminal = new Terminal();
    $result = $terminal->createProcess(['pwd']);
    $this->assertInstanceOf(Process::class, $result);
  }

}
