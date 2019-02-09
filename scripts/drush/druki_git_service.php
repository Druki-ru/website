<?php

/**
 * @file
 * Testing file for git service.
 */

/** @var \Drupal\druki_git\Service\GitInterface $git */
$git = \Drupal::service('druki_git');
//$git->pull();
$git->init();
dump($git->getFileLastCommitId('docs/ru/the-drupal-way.md'));