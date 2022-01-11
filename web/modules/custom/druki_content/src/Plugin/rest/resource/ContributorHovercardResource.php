<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Template\TwigEnvironment;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides endpoint for Contributor Hovercard.
 *
 * @RestResource (
 *   id = "druki_content_contributor_hovercard",
 *   label = @Translation("Contributor Hovercard"),
 *   uri_paths = {
 *     "canonical" = "/api/contributor/hovercard",
 *   }
 * )
 */
final class ContributorHovercardResource extends ResourceBase {

  /**
   * The author storage.
   */
  protected ContentEntityStorageInterface $authorStorage;

  /**
   * The renderer.
   */
  protected RendererInterface $renderer;

  /**
   * The Twig environment.
   */
  protected TwigEnvironment $twig;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
    );
    $instance->authorStorage = $container->get('entity_type.manager')->getStorage('druki_author');
    $instance->renderer = $container->get('renderer');
    $instance->twig = $container->get('twig');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function permissions(): array {
    return [];
  }

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response with content.
   */
  public function get(Request $request): ResourceResponse {
    $build = [];

    if ($request->query->has('author-id')) {
      $author_id = $request->query->get('author-id');
      $build = $this->prepareAuthorHovercard($author_id);
    }
    elseif ($request->query->has('username')) {
      $build = [
        '#theme' => 'druki_content_contributor_hovercard',
        '#display_name' => $request->query->get('username'),
        '#avatar' => [
          '#type' => 'druki_avatar_placeholder',
          '#username' => $request->query->get('username'),
        ],
      ];
    }

    if (!$build) {
      return new ResourceResponse(['@todo wrong request status']);
    }

    $cacheable_metadata = CacheableMetadata::createFromRenderArray($build);

    // We suppress Twig debugging for rest response.
    if ($this->twig->isDebug()) {
      $this->twig->disableDebug();
      $result = $this->renderer->renderPlain($build);
      $this->twig->enableDebug();
    }
    else {
      $result = $this->renderer->renderPlain($build);
    }
    $response = new ResourceResponse([
      'markup' => $result,
    ]);
    $response->addCacheableDependency($cacheable_metadata);
    return $response;
  }

  /**
   * Prepares hovercard for author entity.
   *
   * @param string $author_id
   *   An author ID.
   *
   * @return array|null
   *   An array with hovercard contents, NULL if author is not valid.
   */
  protected function prepareAuthorHovercard(string $author_id): ?array {
    /** @var \Drupal\druki_author\Entity\AuthorInterface $author */
    $author = $this->authorStorage->load($author_id);
    if (!$author) {
      return NULL;
    }

    $display_name_parts = [
      $author->getNameGiven(),
      $author->getNameFamily(),
    ];
    $build = [
      '#theme' => 'druki_content_contributor_hovercard',
      '#is_author' => TRUE,
      '#display_name' => \implode(' ', $display_name_parts),
      '#username' => $author->id(),
      '#about' => $author->get('description')->view([
        'type' => 'druki_author_description',
        'label' => 'hidden',
      ]),
      '#avatar' => $author->get('image')->view([
        'type' => 'druki_author_avatar',
        'label' => 'hidden',
        'settings' => [
          'image_style' => '60_60',
        ],
      ]),
    ];

    $cacheable_metadata = new CacheableMetadata();
    $cacheable_metadata->addCacheableDependency($author);
    $cacheable_metadata->applyTo($build);

    return $build;
  }

}
