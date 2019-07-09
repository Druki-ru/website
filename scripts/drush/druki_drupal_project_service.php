<?php

/** @var \Drupal\druki\Service\DrupalProjects $service */
$service = \Drupal::service('druki.drupal_projects');
dump($service->getCoreLastStableVersion('drupal'));
dump($service->getCoreLastMinorVersion('drupal'));
