<?php

namespace Druki\Tests\Functional\Controller;

use Drupal\Component\DependencyInjection\Container;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\druki_git\Controller\DrukiGitController;
use Drupal\druki_git\Git\GitInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides tests for druki git controller.
 *
 * @coversDefaultClass \Drupal\druki_git\Controller\DrukiGitController
 */
final class DrukiGitControllerTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Test how controller is responding to requests.
   *
   * @covers ::webhook
   */
  public function testResponse(): void {
    $logger = $this->prophesize(LoggerChannelInterface::class);
    $git = $this->prophesize(GitInterface::class);

    $container = new Container();
    $container->set('logger.channel.druki_git', $logger->reveal());
    $container->set('druki_git', $git->reveal());
    $controller = DrukiGitController::create($container);

    $request = $this->prophesize(Request::class);
    // It doesn't matter what is passed. If controller passed access checks, it
    // always respond with HTTP 200 and the same response.
    $request->getContent()->willReturn(\json_encode([]));
    $response = $controller->webhook($request->reveal());
    $this->assertTrue($response instanceof JsonResponse);
    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('{"message":"Webhook processed."}',  $response->getContent());
  }

}
