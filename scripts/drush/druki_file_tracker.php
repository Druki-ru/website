<?php

$file_storage = \Drupal::service('entity_type.manager')->getStorage('file');
/** @var \Drupal\file\FileInterface $file */
$file = $file_storage->load(1);
/** @var \Drupal\druki_file\Service\DrukiFileTracker $tracker */
$tracker = \Drupal::service('druki_file.tracker');
dump($tracker->updateTrackingInformation());