<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Access;

use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\State\StateInterface;
use Drupal\druki_content\Access\ContentWebhookAccess;
use Drupal\druki_content\Repository\ContentSettingsInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Routing\Route;

/**
 * Provides test for content webhook route access check.
 *
 * @coversDefaultClass \Drupal\druki_content\Access\ContentWebhookAccess
 */
final class ContentWebhookAccessTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Builds a mock for content webhook settings.
   *
   * @return \Drupal\druki_content\Repository\ContentSettingsInterface
   *   A mock instance.
   */
  public function buildContentWebhookSettings(): ContentSettingsInterface {
    $webhook_settings = $this->prophesize(ContentSettingsInterface::class);
    $webhook_settings->getContentUpdateWebhookAccessKey()->willReturn('allowed');
    return $webhook_settings->reveal();
  }

  /**
   * Builds mock for state storage.
   *
   * @return \Drupal\Core\State\StateInterface
   *   The mock instance.
   */
  public function buildState(): StateInterface {
    $state = $this->prophesize(StateInterface::class);
    $state->get('system.maintenance_mode')->willReturn(FALSE);
    $state->set('system.maintenance_mode', Argument::any())->will(static function ($args) use ($state): void {
      $state->get('system.maintenance_mode')->willReturn($args[1]);
    });
    return $state->reveal();
  }

  /**
   * Builds mock for webhook route.
   *
   * @return \Symfony\Component\Routing\Route
   *   The mock instance.
   */
  public function buildRoute(): Route {
    $route = $this->prophesize(Route::class);
    $route->getRequirement('_druki_content_webhook_access_key')->willReturn('content_update');
    $route->setRequirement('_druki_content_webhook_access_key', Argument::any())->will(static function ($args) use ($route): void {
      $route->getRequirement('_druki_content_webhook_access_key')->willReturn($args[1]);
    });
    return $route->reveal();
  }

  /**
   * Tests that access check works properly.
   */
  public function testAccess(): void {
    $state = $this->buildState();
    $access_check = new ContentWebhookAccess($this->buildContentWebhookSettings(), $state);
    $route = $this->buildRoute();

    $state->set('system.maintenance_mode', TRUE);
    $this->assertInstanceOf(AccessResultForbidden::class, $access_check->access($route, 'doesnt matter'));
    $state->set('system.maintenance_mode', FALSE);

    $route->setRequirement('_druki_content_webhook_access_key', 'not existed');
    $this->assertInstanceOf(AccessResultForbidden::class, $access_check->access($route, 'doesnt matter'));

    $route->setRequirement('_druki_content_webhook_access_key', 'content_update');
    $this->assertInstanceOf(AccessResultForbidden::class, $access_check->access($route, 'invalid'));
    $this->assertInstanceOf(AccessResultAllowed::class, $access_check->access($route, 'allowed'));
  }

}
