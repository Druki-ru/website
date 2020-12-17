<?php

namespace Druki\Tests\Functional\Access;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\State\StateInterface;
use Drupal\druki_git\Access\DrukiGitWebhookAccess;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides test for git webhook access checker.
 *
 * @coversDefaultClass \Drupal\druki_git\Access\DrukiGitWebhookAccess
 */
final class DrukiGitWebhookAccessTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests all possible access results.
   */
  public function testAccess(): void {
    $valid_key = Crypt::randomBytesBase64();
    $state = $this->prophesize(StateInterface::class);
    $state->get('druki_git.webhook_key')->willReturn($valid_key);
    $state->get('system.maintenance_mode')->willReturn(FALSE);

    $webhook_access = new DrukiGitWebhookAccess($state->reveal());
    $this->assertTrue($webhook_access->access($valid_key) instanceof AccessResultAllowed);
    $this->assertTrue($webhook_access->access('random-value') instanceof AccessResultForbidden);

    // Turn on maintenance mode.
    $state->get('system.maintenance_mode')->willReturn(TRUE);
    $this->assertTrue($webhook_access->access($valid_key) instanceof AccessResultForbidden);
  }

}
