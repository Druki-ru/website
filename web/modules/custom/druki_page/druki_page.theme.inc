<?php

/**
 * @file
 * Custom theme hook preprocess.
 */

/**
 * Prepares variables for druki wiki page templates.
 *
 * Default template: druki-page-wiki.html.twig.
 *
 * @param array $variables
 *   An associative array containing default theme hook variables.
 */
function template_preprocess_druki_page_wiki(array &$variables) {
  $variables['user_guide'] = [
    '#theme' => 'druki_official_user_guide',
  ];
}
