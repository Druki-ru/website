<?php

namespace Drupal\druki_content\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\Repository\DrukiContentStorage;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an extra field to display next and prev links.
 *
 * @ExtraFieldDisplay(
 *   id = "next_prev",
 *   label = @Translation("Next and previous links"),
 *   bundles = {
 *     "druki_content.druki_content",
 *   }
 * )
 */
final class NextPrev extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  /**
   * The druki content storage.
   */
  private DrukiContentStorage $contentStorage;

  /**
   * Constructs a new NextPrev object.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \Drupal\druki_content\Repository\DrukiContentStorage $content_storage
   *   The druki content storage.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, DrukiContentStorage $content_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->contentStorage = $content_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('druki_content'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    if (!$entity->hasField('category')) {
      return [];
    }

    if ($entity->get('category')->isEmpty()) {
      return [];
    }

    return [
      '#type' => 'druki_content_next_prev',
      '#prev_link' => $this->getLink($entity, 'prev'),
      '#next_link' => $this->getLink($entity, 'next'),
      '#cache' => [
        'contexts' => ['url.path'],
      ],
    ];
  }

  /**
   * Gets link to next or previous content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $entity
   *   The entity for relative search.
   * @param string $direction
   *   The link direction. Can be: "next" or "prev".
   *
   * @return \Drupal\Core\Link|null
   *   The link object to next or prev content, NULL if not found.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  protected function getLink(DrukiContentInterface $entity, string $direction): ?Link {
    if (!\in_array($direction, ['next', 'prev'])) {
      return NULL;
    }

    $sort_direction = $direction == 'next' ? 'ASC' : 'DESC';
    $order_condition_operator = $sort_direction == 'ASC' ? '>' : '<';

    $query = $this
      ->contentStorage
      ->getQuery()
      ->condition('category.area', $entity->get('category')->area)
      ->condition('category.order', $entity->get('category')->order, $order_condition_operator)
      ->condition('internal_id', $entity->id(), '!=');

    if (!$entity->get('core')->isEmpty()) {
      $query->condition('core', $entity->getCore());
    }

    $result = $query
      ->range(0, 1)
      ->sort('category.order', $sort_direction)
      ->execute();

    if (empty($result)) {
      return NULL;
    }

    $entity_id = \reset($result);
    $result_entity = $this->contentStorage->load($entity_id);

    return $result_entity->toLink($result_entity->get('category')->title);
  }

}
