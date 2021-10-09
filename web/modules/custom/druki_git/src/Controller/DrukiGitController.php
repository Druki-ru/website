<?php

namespace Drupal\druki_git\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\druki_git\Git\GitInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Druki — git routes.
 */
final class DrukiGitController implements ContainerInjectionInterface {

  /**
   * The logger.
   */
  protected LoggerChannelInterface $logger;

  /**
   * The git.
   */
  protected GitInterface $git;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): object {
    $instance = new static();
    $instance->logger = $container->get('logger.channel.druki_git');
    $instance->git = $container->get('druki_git');

    return $instance;
  }

  /**
   * Reacts on webhook route call.
   */
  public function webhook(Request $request): JsonResponse {
    $webhook_info = \json_decode($request->getContent());

    if (isset($webhook_info->pusher)) {
      if ($this->git->pull()) {
        $this->logger->info('Webhook is triggered, and content is successfully pulled.');
      }
      else {
        $this->logger->warning('Webhook is triggered, the repository was found, but pull complete with error.');
      }
    }
    else {
      $this->logger->warning("Webhook is triggered, but git library can't init repository or webhook triggered not by push event.");
    }

    return new JsonResponse(['message' => 'Webhook processed.']);
  }

}
