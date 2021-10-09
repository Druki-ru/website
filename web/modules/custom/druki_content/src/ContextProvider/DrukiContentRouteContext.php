<?php

namespace Drupal\druki_content\ContextProvider;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Sets the current druki content as context on content routes.
 */
final class DrukiContentRouteContext implements ContextProviderInterface {

  /**
   * The route match.
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * Constructs a new DrukiContentRouteContext object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntimeContexts(array $unqualified_context_ids) {
    $context_definition = EntityContextDefinition::create('druki_content')->setRequired(FALSE);
    $value = NULL;
    if (($route_object = $this->routeMatch->getRouteObject()) && ($route_contexts = $route_object->getOption('parameters')) && isset($route_contexts['druki_content'])) {
      if ($druki_content = $this->routeMatch->getParameter('druki_content')) {
        $value = $druki_content;
      }
    }

    $cacheable_metadata = new CacheableMetadata();
    $cacheable_metadata->setCacheContexts(['route']);

    $context = new Context($context_definition, $value);
    $context->addCacheableDependency($cacheable_metadata);

    return ['druki_content' => $context];
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableContexts() {
    $context_definition = EntityContext::fromEntityTypeId('druki_content', new TranslatableMarkup('Druki Content from URL'));
    return ['druki_content' => $context_definition];
  }

}
