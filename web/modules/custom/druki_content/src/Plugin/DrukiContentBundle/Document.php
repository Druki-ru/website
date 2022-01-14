<?php

declare(strict_types=1);

namespace Drupal\druki_content\Plugin\DrukiContentBundle;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_content\Annotation\DrukiContentBundle;
use Drupal\entity\BundleFieldDefinition;

/**
 * Provides 'document' bundle type.
 *
 * This bundle type is used for wiki-alike content.
 *
 * @DrukiContentBundle(
 *   id = "document",
 *   label = @Translation("Document"),
 * )
 */
final class Document extends PluginBase implements DrukiContentBundleInterface {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions(): array {
    $fields = [];

    $fields['category'] = BundleFieldDefinition::create('druki_category')
      ->setLabel(new TranslatableMarkup('Documentation category'));

    return $fields;
  }

}
