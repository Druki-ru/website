<?php

/**
 * @file
 * Testing file for git service.
 */

/** @var \Drupal\druki_git\Service\GitInterface $git */
$git = \Drupal::service('druki.git');
$git->pull();
