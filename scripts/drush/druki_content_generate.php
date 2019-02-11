<?php

/** @var \Drupal\druki_content\DrukiContentStorage $druki_content_storage */
$druki_content_storage = \Drupal::service('entity_type.manager')->getStorage('druki_content');

dump($druki_content_storage->deleteMissing());