<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\Render\Element;
use Drupal\Core\Template\Attribute;
use Drupal\druki\Data\TableOfContents;

/**
 * Prepares variables for druki content templates.
 *
 * Default template: druki-content.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the druki content information
 *   and any fields attached to the entity.
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_druki_content(array &$variables): void {
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }

  $variables['druki_content'] = $variables['elements']['#druki_content'];
  $variables['view_mode'] = $variables['elements']['#view_mode'];
}

/**
 * Implements hook_preprocess_HOOK() for druki-content-toc.html.twig.
 */
function template_preprocess_druki_content_toc(array &$variables): void {
  $toc = $variables['toc'];
  if (!$toc instanceof TableOfContents) {
    return;
  }
  $variables['links'] = $toc->toTreeArray();
}

/**
 * Implements hook_preprocess_HOOK() for druki-content-category-navigation.html.twig.
 */
function template_preprocess_druki_content_category_navigation(array &$variables): void {
  /** @var \Drupal\Core\Path\PathMatcherInterface $path_matcher */
  $path_matcher = \Drupal::service('path.matcher');
  $current_path = \Drupal::service('path.current')->getPath();
  /** @var \Drupal\path_alias\AliasManagerInterface $path_alias_manager */
  $path_alias_manager = \Drupal::service('path_alias.manager');

  foreach ($variables['links'] as &$link) {
    $link['attributes'] = new Attribute();
    $link['is_active'] = $path_matcher->matchPath(
      $current_path,
      $path_alias_manager->getPathByAlias($link['url']->toString()),
    );
  }
}
