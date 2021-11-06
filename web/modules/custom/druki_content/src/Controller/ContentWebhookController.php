<?php

namespace Drupal\druki_content\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\druki_content\Event\RequestSourceContentUpdateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Provides a controller for content webhooks.
 */
final class ContentWebhookController implements ContainerInjectionInterface {

  /**
   * The logger.
   */
  protected LoggerChannelInterface $logger;

  /**
   * The event dispatcher.
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = new self();
    $instance->logger = $container->get('logger.channel.druki_content');
    $instance->eventDispatcher = $container->get('event_dispatcher');
    return $instance;
  }

  /**
   * The content update webhook controller.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The webhook response.
   */
  public function update(): JsonResponse {
    $this->logger->info('Content update webhook successfully triggered.');
    $this->eventDispatcher->dispatch(new RequestSourceContentUpdateEvent());
    return new JsonResponse(['message' => 'Ok.']);
  }

}
