<?php

declare(strict_types=1);

namespace Drupal\druki_content\TypedData;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataDefinitionInterface;
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
    $this->setPropertyDefinition('core', $this->getCoreDefinition());
    $this->setPropertyDefinition('category', $this->getCategoryDefinition());
    $this->setPropertyDefinition('search-keywords', ListDataDefinition::create('string'));
    // @todo metatags.
    return parent::getPropertyDefinitions();
  }

  /**
   * Gets definition for Drupal core.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   A drupal core metadata definition.
   */
  protected function getCoreDefinition(): DataDefinitionInterface {
    return DataDefinition::create('integer')
      ->addConstraint('Range', [
        'min' => 8,
        'max' => 10,
      ]);
  }

  /**
   * Gets category metadata definition.
   *
   * @return \Drupal\Core\TypedData\DataDefinitionInterface
   *   A data definition for category.
   */
  protected function getCategoryDefinition(): DataDefinitionInterface {
    $order_definition = DataDefinition::create('integer')
      ->addConstraint('Range', [
        'min' => 0,
        'max' => 1000,
      ]);

    return MapDataDefinition::create()
      ->setPropertyDefinition('area', DataDefinition::create('string')->setRequired(TRUE))
      ->setPropertyDefinition('order', $order_definition)
      ->setPropertyDefinition('title', DataDefinition::create('string'));
  }

}
