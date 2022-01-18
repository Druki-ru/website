<?php

declare(strict_types=1);

namespace Drupal\druki_content\TypedData;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\ListDataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;

/**
 * A typed data definition for content metadata data.
 */
final class DocumentationMetadataDefinition extends MapDataDefinition {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(): array {
    $this->setPropertyDefinition('title', DataDefinition::create('string')->setRequired(TRUE));
    $this->setPropertyDefinition('slug', DataDefinition::create('string')->setRequired(TRUE));
    $core_definition = DataDefinition::create('integer')
      ->addConstraint('AllowedValues', ['8', '9', '10']);
    $this->setPropertyDefinition('core', $core_definition);
    $this->setPropertyDefinition('category', ContentCategoryDefinition::create());
    $this->setPropertyDefinition('search-keywords', ListDataDefinition::create('string'));
    // @todo metatags.
    return parent::getPropertyDefinitions();
  }

}
