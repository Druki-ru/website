<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_HOOK() for druki-category-navigation.html.twig.
 */
function template_preprocess_druki_category_navigation(array &$variables): void {
  /** @var \Drupal\Core\Path\PathMatcherInterface $path_matcher */
  $path_matcher = \Drupal::service('path.matcher');
  $current_path = \Drupal::service('path.current')->getPath();
  /** @var \Drupal\Core\Path\AliasManagerInterface $path_alias_manager */
  $path_alias_manager = \Drupal::service('path.alias_manager');

  foreach ($variables['links'] as &$link) {
    $link['attributes'] = new Attribute();
    $link['is_active'] = $path_matcher->matchPath(
      $current_path,
      $path_alias_manager->getPathByAlias($link['url']->toString())
    );
  }
}
