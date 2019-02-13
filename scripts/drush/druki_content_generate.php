<?php

/** @var \Drupal\druki_content\DrukiContentStorage $druki_content_storage */
$druki_content_storage = \Drupal::service('entity_type.manager')->getStorage('druki_content');
/** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
$druki_content = $druki_content_storage->load('installation');
dump($druki_content->getTitle());
dump($druki_content->getRelativePathname());
dump($druki_content->getFilename());
dump($druki_content->getLastCommitId());
dump($druki_content->getContributionStatistics());