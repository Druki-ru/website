<?php

declare(strict_types=1);

namespace Druki\Tests\Unit\Controller;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\druki_content\Controller\ContentWebhookController;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides test for content webhook controller.
 *
 * @coversDefaultClass \Drupal\druki_content\Controller\ContentWebhookController
 */
final class ContentWebhookControllerTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that content update controller works as expected.
   */
  public function testUpdate(): void {
    $controller = $this->buildController();

    $response = $controller->update();
    $this->assertInstanceOf(JsonResponse::class, $response);
    $this->assertEquals('{"message":"Ok."}', $response->getContent());
  }

  /**
   * Builds an instance for controller with mocked dependencies.
   *
   * @return \Drupal\druki_content\Controller\ContentWebhookController
   *   The controller instance.
   */
  protected function buildController(): ContentWebhookController {
    $logger = $this->prophesize(LoggerChannelInterface::class);
    $container = $this->prophesize(ContainerInterface::class);
    $container->get('logger.channel.druki_content')->willReturn($logger->reveal());

    return ContentWebhookController::create($container->reveal());
  }

}
