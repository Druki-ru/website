<?php

/** @var \Drupal\druki_content\DrukiContentStorage $druki_content_storage */
$druki_content_storage = \Drupal::service('entity_type.manager')->getStorage('druki_content');
$druki_content = $druki_content_storage->load('faq');
dump($druki_content->get('contribution_statistics')->getValue());