<?php

/**
 * @file
 * Contains update hooks.
 */

/**
 * Moves state value for force content import from the old state to the new.
 *
 * Sync code was moved from druki_content entity module to standalone
 * druki_content_sync, so state variable is changed.
 */
function druki_content_sync_post_update_8701(array &$sandbox) {
  $state = \Drupal::state();
  $old_value = $state->get('druki_content.settings.force_update', FALSE);
  // Save to new variable.
  $state->set('druki_content_sync.settings.force_update', $old_value);
}
