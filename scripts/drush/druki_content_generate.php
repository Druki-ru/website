<?php

/** @var \Drupal\druki_content\DrukiContentStorage $druki_content_storage */
$druki_content_storage = \Drupal::service('entity_type.manager')->getStorage('druki_content');
/** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
$druki_content = $druki_content_storage->load(31);
dump(unserialize($druki_content->get('metatags')->value));
//dump($druki_content->getTitle());
//dump($druki_content->getRelativePathname());
//dump($druki_content->getFilename());
//dump($druki_content->getLastCommitId());
//dump($druki_content->getContributionStatistics());
//$all = $druki_content_storage->loadMultiple();
//$druki_content_storage->delete($all);
/** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
//$druki_content = $druki_content_storage->loadByMeta('faq', NULL);
//$druki_content->save();

// Test TOC query.
//$result = $druki_content_storage->getQuery()
//  ->condition('toc.area', 'drupal')
//  ->execute();
//dump($result);

///** @var \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager */
//$field_manager = \Drupal::service('entity_field.manager');
//$field_definitions = $field_manager->getFieldDefinitions('druki_content', 'druki_content');
//$difficulty = $field_definitions['difficulty'];
//$settings = $difficulty->getSetting('allowed_values');
//dump(array_keys($settings));

//$user_storage = \Drupal::entityTypeManager()->getStorage('user');
//$user = $user_storage->load(1);
//dump(filter_default_format($user));
