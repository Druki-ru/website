<?php

namespace Drupal\druki_content\Routing;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\druki_content\Entity\ContentInterface;
use Drupal\druki_content\Repository\ContentSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class to redirect from entity to remote resources.
 */
final class ContentRedirectController extends ControllerBase {

  /**
   * The content settings.
   */
  protected ContentSettingsInterface $contentSettings;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = new self();
    $instance->contentSettings = $container->get('druki_content.repository.content_settings');
    return $instance;
  }

  /**
   * Redirects to remote url.
   *
   * @param \Drupal\druki_content\Entity\ContentInterface $druki_content
   *   The druki content entity.
   * @param string $redirect_to
   *   The requested redirect.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The redirect response.
   */
  public function build(ContentInterface $druki_content, string $redirect_to): Response {
    $repository_url = $this->contentSettings->getRepositoryUrl();
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
