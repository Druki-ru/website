<?php

namespace Drupal\druki_content\Entity\Handler\RouteProvider;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_git\Git\GitSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class to redirect from entity to remote resources.
 */
final class DrukiContentRedirectController extends ControllerBase {

  /**
   * The git settings.
   */
  private GitSettingsInterface $gitSettings;

  /**
   * Constructs a new DrukiContentRedirectController object.
   *
   * @param \Drupal\druki_git\Git\GitSettingsInterface $git_settings
   *   The git settings.
   */
  public function __construct(GitSettingsInterface $git_settings) {
    $this->gitSettings = $git_settings;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('druki_git.settings'),
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
  public function build(DrukiContentInterface $druki_content, string $redirect_to): Response {
    $repository_url = $this->gitSettings->getRepositoryUrl();
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
