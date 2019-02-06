<?php

$file_storage = \Drupal::service('entity_type.manager')->getStorage('file');
$file = $file_storage->load(1);
/** @var \Drupal\druki_file\Service\DrukiFileTracker $tracker */
$tracker = \Drupal::service('druki_file.tracker');
dump($tracker->isFileTracked($file));