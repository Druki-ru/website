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
 *     "storage" = "Drupal\druki_content\DrukiContentStorage",
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
 *     "id" = "internal_id",
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {

    $fields = parent::baseFieldDefinitions($entity_type);

    // The string entity id used in source files.
    $fields['external_id'] = BaseFieldDefinition::create('string')
      ->setLabel('External content ID')
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

    $fields['relative_pathname'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Relative pathname'))
      ->setDescription(t('The pathname of source file.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['filename'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Filename'))
      ->setDescription(t('The filename of source file.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['last_commit_id'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(t('Last commit ID'))
      ->setDescription(t('The last commit ID of source file.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['contribution_statistics'] = BaseFieldDefinition::create('map')
      ->setTranslatable(TRUE)
      ->setLabel(t('Contribution statistics'))
      ->setDescription(t('The contribution statistics of source file.'))
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE);

    $fields['core'] = BaseFieldDefinition::create('string')
      ->setLabel('The core version for this content.')
      ->setRequired(FALSE)
      ->setReadOnly(TRUE);

    $fields['toc'] = BaseFieldDefinition::create('druki_toc')
      ->setLabel('The TOC.')
      ->setRequired(FALSE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTagsToInvalidate(): array {
    $cache_tags = parent::getCacheTagsToInvalidate();
    $langcode = $this->getEntityKey('langcode');

    // F.e. "druki_content:ru:1", "druki_content:ru:installation".
    $cache_tags[] = $this->entityTypeId . ':' . $langcode . ':' . $this->id();
    $cache_tags[] = $this->entityTypeId . ':' . $langcode . ':' . $this->getExternalId();

    return $cache_tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getExternalId(): string {
    return $this->get('external_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle(): string {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle(string $title): DrukiContentInterface {
    $this->set('title', $title);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelativePathname(): string {
    return $this->get('relative_pathname')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRelativePathname(string $relative_pathname): DrukiContentInterface {
    $this->set('relative_pathname', $relative_pathname);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFilename(): string {
    return $this->get('filename')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFilename(string $filename): DrukiContentInterface {
    $this->set('filename', $filename);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastCommitId(): string {
    return $this->get('last_commit_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLastCommitId(string $commit_id): DrukiContentInterface {
    $this->set('last_commit_id', $commit_id);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContributionStatistics(): array {
    return $this->get('contribution_statistics')->first()->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function setContributionStatistics(array $contribution_statistics): DrukiContentInterface {
    $this->set('contribution_statistics', serialize($contribution_statistics));

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setCore(string $core): DrukiContentInterface {
    $this->set('core', $core);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCore(): ?string {
    return $this->get('core')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTOC(string $area, int $order = 0): DrukiContentInterface {
    $this->set('toc', [
      'area' => $area,
      'order' => $order,
    ]);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTOC(): array {
    return $this->get('toc')->first()->getValue();
  }

}
