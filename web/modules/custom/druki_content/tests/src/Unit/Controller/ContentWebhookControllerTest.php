<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Unit\Controller;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\druki_content\Controller\ContentWebhookController;
use Drupal\druki_content\Event\ContentSourceUpdateRequestEvent;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Provides test for content webhook controller.
 *
 * @coversDefaultClass \Drupal\druki_content\Controller\ContentWebhookController
 */
final class ContentWebhookControllerTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * An array with dispatched events via mock.
   */
  protected array $dispatchedEvents = [];

  /**
   * Tests that content update controller works as expected.
   */
  public function testUpdate(): void {
    $controller = $this->buildController();

    $response = $controller->update();
    $this->assertInstanceOf(JsonResponse::class, $response);
    $this->assertEquals('{"message":"Ok."}', $response->getContent());
    $this->assertContains(ContentSourceUpdateRequestEvent::class, $this->dispatchedEvents);
  }

  /**
   * Builds an instance for controller with mocked dependencies.
   *
   * @return \Drupal\druki_content\Controller\ContentWebhookController
   *   The controller instance.
   */
  protected function buildController(): ContentWebhookController {
    $self = $this;
    $this->dispatchedEvents = [];
    $logger = $this->prophesize(LoggerChannelInterface::class);
    $container = $this->prophesize(ContainerInterface::class);
    $container->get('logger.channel.druki_content')->willReturn($logger->reveal());

    $event_dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $event_dispatcher->dispatch(Argument::any())->will(static function ($args) use ($self): void {
      $self->dispatchedEvents[] = $args[0]::class;
    });
    $container->get('event_dispatcher')->willReturn($event_dispatcher->reveal());

    return ContentWebhookController::create($container->reveal());
  }

}
