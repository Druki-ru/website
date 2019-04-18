<?php

namespace Drupal\druki_git\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\druki_git\Service\GitInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Returns responses for Druki — git routes.
 */
class DrukiGitController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

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

  public function __construct(Request $request, LoggerChannelInterface $logger, GitInterface $git) {
    $this->request = $request;
    $this->logger = $logger;
    $this->git = $git;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container): object {
    return new static(
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('logger.factory')->get('druki_git'),
      $container->get('druki_git')
    );
  }

  /**
   * Reacts on webhook route call.
   */
  public function webhook(): JsonResponse {
    $webhook_info = json_decode($this->request->getContent());

    if ($this->git->init() && $webhook_info->object_kind == 'push') {
      if ($this->git->pull()) {
        $this->logger->info(t('Webhook is triggered, and content is successfully pulled.'));
      }
      else {
        $this->logger->warning(t('Webhook is triggered, the repository was found, but pull complete with error.'));
      }
    }
    else {
      $this->logger->warning(t("Webhook is triggered, but git library can't init repository or webhook triggered not by push event."));
    }

    return new JsonResponse(['message' => 'Webhook processed.']);
  }

}
