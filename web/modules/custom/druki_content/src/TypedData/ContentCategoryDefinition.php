<?php

declare(strict_types=1);

namespace Drupal\druki_content\TypedData;

use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;

/**
 * A typed data definition for content category.
 */
final class ContentCategoryDefinition extends MapDataDefinition {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(): array {
    $this->setPropertyDefinition('area', DataDefinition::create('string')->setRequired(TRUE));
    $order_definition = DataDefinition::create('integer')
      ->addConstraint('Range', [
        'min' => 0,
        'max' => 1000,
      ]);
    $this->setPropertyDefinition('order', $order_definition);
    $this->setPropertyDefinition('title', DataDefinition::create('string'));
    return parent::getPropertyDefinitions();
  }

}
