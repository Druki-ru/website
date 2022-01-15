<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\Content\ContentType;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\entity\BundleFieldDefinition;

/**
 * Provides 'document' bundle type.
 *
 * This bundle type is used for wiki-alike content.
 *
 * @ContentType(
 *   id = "documentation",
 *   label = @Translation("Documentation"),
 * )
 */
final class Documentation extends PluginBase implements ContentTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions(): array {
    $fields = [];

    $fields['category'] = BundleFieldDefinition::create('druki_category')
      ->setLabel(new TranslatableMarkup('Documentation category'));

    $fields['search_keywords'] = BundleFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Search keywords'))
      ->setDescription(new TranslatableMarkup('The additional or special search keywords which will boost content in search.'));

    $fields['metatags'] = BundleFieldDefinition::create('metatag')
      ->setLabel(new TranslatableMarkup('Metatags'))
      ->setDescription(new TranslatableMarkup('An addition meta-information for content used by search engines and social media.'));

    return $fields;
  }

}
