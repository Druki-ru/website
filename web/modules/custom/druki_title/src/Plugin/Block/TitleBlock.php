<?php

namespace Drupal\druki_title\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a the title block.
 *
 * @Block(
 *   id = "druki_title",
 *   admin_label = @Translation("Page title"),
 *   category = @Translation("Druki Title")
 * )
 */
class TitleBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * The title resolver.
   *
   * @var \Drupal\Core\Controller\TitleResolverInterface
   */
  protected $titleResolver;

  /**
   * Constructs a new TitleBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $route_match
   *   The route match.
   * @param \Drupal\Core\Controller\TitleResolverInterface $title_resolver
   *   The title resolver.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RequestStack $request_stack, CurrentRouteMatch $route_match, TitleResolverInterface $title_resolver) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->request = $request_stack->getCurrentRequest();
    $this->routeMatch = $route_match;
    $this->titleResolver = $title_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('request_stack'),
      $container->get('current_route_match'),
      $container->get('title_resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build['content'] = [
      '#theme' => 'druki_title',
      '#title' => $this->getPageTitle(),
      '#links' => $this->getLinks(),
    ];

    return $build;
  }

  /**
   * Gets current page title.
   *
   * @return string
   *   The page title.
   */
  protected function getPageTitle(): ?string {
    return $this->titleResolver->getTitle($this->request, $this->routeMatch->getRouteObject());
  }

  /**
   * Gets additional links for title block.
   *
   * @code
   * [
   *  'edit' => [
   *    'label' => 'Edit',
   *    'url' => '/some/edit/url',
   *    'attributes' => [
   *       'target' => '_blank',
   *     ],
   *   ],
   * ]
   * @endcode
   *
   * @return array
   *   The links array.
   * @see template_preprocess_druki_title().
   *
   */
  protected function getLinks(): array {
    $links = [];

    foreach ($this->routeMatch->getParameters() as $parameter) {
      if ($parameter instanceof DrukiContentInterface) {
        $this->processDrukiContentLinks($parameter, $links);
      }
    }

    return $links;
  }

  /**
   * Handle druki_content entity links.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The entity.
   * @param array $links
   *   The current array with links.
   */
  protected function processDrukiContentLinks(DrukiContentInterface $druki_content, array &$links): void {
    $links['edit'] = [
      'label' => t('Edit'),
      // @todo improve it. Possible solutions:
      // 1. Method for Druki entity.
      // 2. Dynamic links generator.
      'url' => 'https://gitlab.com/druki/druki-content-dev/edit/master/' . $druki_content->getRelativePathname(),
      'attributes' => [
        'rel' => 'nofollow noopener',
      ],
    ];
  }

}
