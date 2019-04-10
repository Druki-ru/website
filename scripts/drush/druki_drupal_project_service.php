<?php

/** @var \Drupal\druki\Service\DrupalProjects $service */
$service = \Drupal::service('druki.drupal_projects');
dump($service->getProjectLastStableRelease('drupal', '8.x'));