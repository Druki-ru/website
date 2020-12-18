<?php

namespace Drupal\druki\EventSubscriber;

use Drupal\imagemagick\Event\ImagemagickExecutionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Provides Imagemagick event subscriber.
 */
final class ImagemagickEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      ImagemagickExecutionEvent::PRE_CONVERT_EXECUTE => 'preConvertExecute',
    ];
  }

  /**
   * Fires before the 'convert' command is executed.
   *
   * @param \Drupal\imagemagick\Event\ImagemagickExecutionEvent $event
   *   The execution event.
   */
  public function preConvertExecute(ImagemagickExecutionEvent $event): void {
    $arguments = $event->getExecArguments();
    // https://developers.google.com/speed/docs/insights/OptimizeImages
    $arguments->add('-sampling-factor 4:2:0');
    // Progression JPEG and interlaced PNGs support.
    $arguments->add('-interlace Plane');
    // Clean image for all unused data. I.e. EXIF.
    $arguments->add('-strip');
  }

}
