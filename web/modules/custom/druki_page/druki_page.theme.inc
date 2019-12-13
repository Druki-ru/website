<?php

/**
 * @file
 *
 * Custom theme hook preprocess.
 */

/**
 * Implements hook_preprocess_HOOK() for 'druki-page-wiki.html.twig'.
 */
function template_preprocess_druki_page_wiki(array &$variables) {
  $variables['user_guide'] = [
    '#theme' => 'druki_official_user_guide',
  ];
}
