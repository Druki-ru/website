<?php

namespace Druki\Tests\Functional\EventSubscriber;

use Drupal\druki\EventSubscriber\ImagemagickEventSubscriber;
use Drupal\imagemagick\Event\ImagemagickExecutionEvent;
use Drupal\imagemagick\ImagemagickExecArguments;
use Drupal\imagemagick\ImagemagickExecManagerInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Provides tests for ImagemagickEventSubscriber.
 *
 * @coversDefaultClass \Drupal\druki\EventSubscriber\ImagemagickEventSubscriber
 */
final class ImagemagickEventSubscriberTest extends UnitTestCase {

  use ProphecyTrait;

  /**
   * Tests that arguments are added.
   *
   * @covers ::preConvertExecute
   */
  public function testArguments(): void {
    $exec_manager = $this->prophesize(ImagemagickExecManagerInterface::class);
    $exec_arguments = new ImagemagickExecArguments($exec_manager->reveal());
    $event = new ImagemagickExecutionEvent($exec_arguments);

    $event_subscriber = new ImagemagickEventSubscriber();
    $event_subscriber->preConvertExecute($event);

    $arguments = $event->getExecArguments();
    $this->assertEquals('-sampling-factor 4:2:0 -interlace Plane -strip', $arguments->toString(ImagemagickExecArguments::POST_SOURCE));
  }

}
