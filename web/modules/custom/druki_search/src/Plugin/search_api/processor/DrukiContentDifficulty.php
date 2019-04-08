<?php

namespace Drupal\druki_search\Plugin\search_api\processor;

use Drupal\Core\Entity\EntityInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * @SearchApiProcessor(
 *   id = "druki_content_difficulty",
 *   label = @Translation("Druki content difficulty"),
 *   description = @Translation("Processed difficulty value."),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   hidden = true,
 *   locked = true,
 * )
 */
class DrukiContentDifficulty extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index): bool {
    foreach ($index->getDatasources() as $datasource) {
      if ($datasource->getEntityTypeId() == 'druki_content') {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL): array {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Druki Content Difficulty'),
        'description' => $this->t('Processed difficulty value.'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['druki_content_difficulty'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item): void {
    /** @var EntityInterface $entity */
    $entity = $item->getOriginalObject()->getValue();

    if ($entity instanceof DrukiContentInterface && $entity->hasField('difficulty')) {
      $difficulty = $entity->get('difficulty')->value;

      // Default value if difficulty is not set.
      if (!$difficulty) {
        $difficulty = 'none';
      }

      // Add value to index.
      $fields = $this
        ->getFieldsHelper()
        ->filterForPropertyPath(
          $item->getFields(),
          NULL,
          'druki_content_difficulty'
        );

      foreach ($fields as $field) {
        $field->addValue($difficulty);
      }
    }
  }

}
