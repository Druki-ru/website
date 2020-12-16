<?php

namespace Druki\Tests\Unit\Git;

use Drupal\druki_git\Git\GitHelper;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Process\Process;

final class GitHelperTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that helper is pulling data.
   */
  public function testPull(): void {
    $process = $this->prophesize(Process::class);
    $process->run()->willReturn('test');
    $process->reveal();

    GitHelper::pull('test');
  }

}
