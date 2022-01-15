<?php

namespace Drupal\druki_content\Entity;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki\Data\Contributor;
use Drupal\druki\Data\ContributorList;
use Drupal\druki_content\Data\ContentDocument;
use Drupal\entity\BundleFieldDefinition;

/**
 * Defines the druki content entity class.
 *
 * @ContentEntityType(
 *   id = "druki_content",
 *   label = @Translation("Druki content"),
 *   label_collection = @Translation("Druki content"),
 *   bundle_label = @Translation("Druki content bundle"),
 *   handlers = {
 *     "storage" = "Drupal\druki_content\Repository\ContentStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\druki_content\Controller\ContentListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\druki_content\Access\ContentAccessControlHandler",
 *     "redirect_controller" = "Drupal\druki_content\Routing\ContentRedirectController",
 *     "form" = {
 *       "edit" = "Drupal\druki_content\Form\ContentForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-all" = "Drupal\druki_content\Form\ContentDeleteAllForm",
 *       "settings" = "Drupal\druki_content\Form\ContentSettingsForm",
 *       "sync" = "\Drupal\druki_content\Form\ContentSyncForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\druki_content\Routing\ContentHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "druki_content",
 *   data_table = "druki_content_field_data",
 *   bundle_plugin_type = "druki_content_type",
 *   translatable = TRUE,
 *   admin_permission = "administer druki content",
 *   entity_keys = {
 *     "id" = "internal_id",
 *     "langcode" = "langcode",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *   },
 *   links = {
 *     "canonical" = "/druki_content/{druki_content}",
 *     "edit-form" = "/admin/druki/content/{druki_content}/edit",
 *     "delete-form" = "/admin/druki/content/{druki_content}/delete",
 *     "delete-all-form" = "/admin/druki/content/delete-all",
 *     "collection" = "/admin/content/druki-content",
 *     "edit-remote" = "/druki_content/{druki_content}/edit-remote",
 *     "history-remote" = "/druki_content/{druki_content}/history-remote",
 *     "settings" = "/admin/structure/druki-content",
 *     "sync" = "/admin/druki/content/sync"
 *   },
 * )
 */
final class Content extends ContentEntityBase implements ContentInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['slug'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('The content slug'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setTranslatable(TRUE)
      ->setLabel(new TranslatableMarkup('Title'))
      ->setDescription(new TranslatableMarkup('The title of the druki content entity.'))
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
      ->setLabel(new TranslatableMarkup('Relative pathname'))
      ->setDescription(new TranslatableMarkup('The pathname of source file.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['core'] = BaseFieldDefinition::create('string')
      ->setLabel('The core version for this content.')
      ->setRequired(FALSE)
      ->setReadOnly(TRUE);

    $fields['source_hash'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Source content hash'))
      ->setDescription(new TranslatableMarkup('Store the last parsed content hash used for current content.'))
      ->setRequired(FALSE)
      ->setSetting('max_length', 255)
      ->setReadOnly(TRUE);

    $fields['document'] = BundleFieldDefinition::create('druki_content_document')
      ->setLabel(new TranslatableMarkup('The content document.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['contributors'] = BaseFieldDefinition::create('druki_contributor')
      ->setLabel(new TranslatableMarkup('The content contributors.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    return $fields;
  }

  /**
   * {@inheritdoc}
   *
   * @todo Evaluate which of this is not needed anymore and simplify.
   */
  public function getCacheTagsToInvalidate(): array {
    $cache_tags = parent::getCacheTagsToInvalidate();
    $langcode = $this->getEntityKey('langcode');

    // F.e. "druki_content:ru:1", "druki_content:ru:installation".
    if ($this->isNew()) {
      $cache_tags[] = $this->entityTypeId . ':' . $langcode . ':' . $this->id();
    }
    $cache_tags[] = $this->entityTypeId . ':' . $langcode . ':' . $this->getSlug();
    $relative_pathname_hash = Crypt::hashBase64($this->getRelativePathname());
    $cache_tags[] = $this->entityTypeId . ':relative_pathname:' . $relative_pathname_hash;

    // Invalidate cache tag for category area block.
    // @see CategoryNavigationBlock::getCacheTags().
    if (!$this->get('category')->isEmpty()) {
      $category_area = $this->get('category')->first()->getCategoryArea();
      $cache_tags[] = 'druki_category_navigation:' . Crypt::hashBase64($category_area);
    }

    return $cache_tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getSlug(): string {
    return $this->get('slug')->getString();
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
  public function getTitle(): string {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle(string $title): ContentInterface {
    $this->set('title', $title);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRelativePathname(string $relative_pathname): ContentInterface {
    $this->set('relative_pathname', $relative_pathname);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setCore(?int $core): ContentInterface {
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
  public function setCategory(string $area, int $order = 0, ?string $title = NULL): ContentInterface {
    $this->set('category', [
      'area' => $area,
      'order' => $order,
      'title' => $title,
    ]);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function unsetCategory(): void {
    $this->set('category', NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function getCategory(): ?array {
    if ($this->get('category')->isEmpty()) {
      return NULL;
    }
    return $this->get('category')->first()->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getSourceHash(): ?string {
    return $this->get('source_hash')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSourceHash(string $hash): ContentInterface {
    $this->set('source_hash', $hash);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setContentDocument(ContentDocument $content_document): self {
    $this->set('document', $content_document);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContentDocument(): ?ContentDocument {
    if ($this->get('document')->isEmpty()) {
      return NULL;
    }
    return $this->get('document')->first()->getContentDocument();
  }

  /**
   * {@inheritdoc}
   */
  public function setContributors(ContributorList $contributors): self {
    $this->unsetContributors();
    foreach ($contributors->getIterator() as $contributor) {
      $this->addContributor($contributor);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function unsetContributors(): void {
    $this->set('contributors', NULL);
  }

  /**
   * {@inheritdoc}
   */
  public function addContributor(Contributor $contributor): self {
    $this->get('contributors')->appendItem([
      'name' => $contributor->getUsername(),
      'email' => $contributor->getEmail(),
    ]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContributors(): ContributorList {
    $contributors = new ContributorList();
    /** @var \Drupal\druki\Plugin\Field\FieldType\ContributorItem $contributor_field_item */
    foreach ($this->get('contributors') as $contributor_field_item) {
      $contributors->addContributor($contributor_field_item->toContributor());
    }
    return $contributors;
  }

}
