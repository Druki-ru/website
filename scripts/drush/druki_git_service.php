<?php

/**
 * @file
 * Testing file for git service.
 */

/** @var \Drupal\druki_git\Service\GitInterface $git */
$git = \Drupal::service('druki_git');
//$git->pull();
$git->init();
dump($git->getFileCommitsInfo('docs/ru/faq.md'));