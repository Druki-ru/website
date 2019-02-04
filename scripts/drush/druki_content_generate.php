<?php

$druki_content_storage = \Drupal::service('entity_type.manager')->getStorage('druki_content');

/** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
$druki_content = $druki_content_storage->create();
$druki_content->set('id', 'drush-test');
$druki_content->set('title', "title Example");
$druki_content->set('relative_pathname', 'docs/ru/test.md');
$druki_content->set('filename', 'test.md');
$druki_content->set('last_commit_id', 'bla-bla-bla');
$druki_content->save();
