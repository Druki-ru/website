<?php

namespace Drupal\druki_content\Entity\Handler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class to redirect from entity to remote resources.
 */
final class DrukiContentRedirectController extends ControllerBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $gitConfig;

  /**
   * Constructs a new DrukiContentRedirectController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->gitConfig = $config_factory->get('druki_git.git_settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Redirects to remote url.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content entity.
   * @param string $redirect_to
   *   The requested redirect.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The redirect response.
   */
  public function build(DrukiContentInterface $druki_content, string $redirect_to) {
    $repository_url = $this->gitConfig->get('repository_url');
    $relative_pathname = $druki_content->getRelativePathname();

    switch ($redirect_to) {
      case 'edit':
        $redirect_url = "$repository_url/edit/master/$relative_pathname";
        return new TrustedRedirectResponse($redirect_url);

      case 'history':
        $redirect_url = "$repository_url/commits/master/$relative_pathname";
        return new TrustedRedirectResponse($redirect_url);
    }

    throw new NotFoundHttpException();
  }

}
