<?php

/**
 * @file
 * Main file for custom theme hook preprocess.
 */

/**
 * Implements hook_preprocess_HOOK() for druki-remote-video-optimized.html.twig.
 */
function template_preprocess_druki_remote_video_optimized(array &$variables): array {
  $variables['attributes']['data-video-provider'] = $variables['video_provider'];
  $variables['attributes']['data-video-id'] = $variables['video_id'];
}
