<?php

namespace Drupal\druki_content\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the druki content entity class.
 *
 * @ContentEntityType(
 *   id = "druki_content",
 *   label = @Translation("Druki content"),
 *   label_collection = @Translation("Druki contents"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\druki_content\DrukiContentListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\druki_content\Form\DrukiContentForm",
 *       "edit" = "Drupal\druki_content\Form\DrukiContentForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "druki_content",
 *   data_table = "druki_content_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer druki content",
 *   entity_keys = {
 *     "id" = "id",
 *     "langcode" = "langcode",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/druki/content/add",
 *     "canonical" = "/druki_content/{druki_content}",
 *     "edit-form" = "/admin/druki/content/{druki_content}/edit",
 *     "delete-form" = "/admin/druki/content/{druki_content}/delete",
 *     "collection" = "/admin/content/druki-content"
 *   },
 *   field_ui_base_route = "entity.druki_content.settings"
 * )
 */
class DrukiContent extends ContentEntityBase implements DrukiContentInterface {

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    // Rewrite entity id. In our case this is string.
    $fields[$entity_type->getKey('id')] = BaseFieldDefinition::create('string')
      ->setLabel($entity_type->getBundleLabel())
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the druki content entity.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
