<?php

namespace Drupal\druki_git\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Druki — git routes.
 *
 * @todo Add tests.
 */
final class DrukiGitController implements ContainerInjectionInterface {

  /**
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * The git.
   *
   * @var \Drupal\druki_git\Service\GitInterface
   */
  protected $git;

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
    $webhook_info = json_decode($request->getContent());

    if ($webhook_info->object_kind == 'push') {
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
