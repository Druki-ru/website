<?php

declare(strict_types=1);

namespace Drupal\druki_author\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\MediaInterface;

/**
 * Provides entity for store author information.
 *
 * @ContentEntityType(
 *   id = "druki_author",
 *   label = @Translation("Author"),
 *   label_collection = @Translation("Authors"),
 *   base_table = "druki_author",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   links = {
 *     "canonical" = "/author/{druki_author}",
 *   }
 * )
 */
final class Author extends ContentEntityBase implements AuthorInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields[$entity_type->getKey('id')] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('ID'))
      ->setReadOnly(TRUE)
      ->setRequired(TRUE);

    $fields['name_given'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Given name'))
      ->setRequired(TRUE);

    $fields['name_family'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Family name'))
      ->setRequired(TRUE);

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Country'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 2);

    $fields['org_name'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Organization name'));

    $fields['org_unit'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Organization unit'));

    $fields['homepage'] = BaseFieldDefinition::create('uri')
      ->setLabel(new TranslatableMarkup('Homepage'));

    $fields['description'] = BaseFieldDefinition::create('map')
      ->setLabel(new TranslatableMarkup('Description'))
      ->setDescription(new TranslatableMarkup('A list of descriptions for author keyed by langcode.'));

    $fields['image'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Image'))
      ->setSetting('target_type', 'media');

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function setId(string $id): self {
    $this->set('id', $id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setName(string $given, string $family): AuthorInterface {
    $this->set('name_given', $given);
    $this->set('name_family', $family);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getNameGiven(): string {
    return $this->get('name_given')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function getNameFamily(): string {
    return $this->get('name_family')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function setCountry(string $country): AuthorInterface {
    $this->set('country', $country);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry(): string {
    return $this->get('country')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function setOrganization(string $name, string $unit): AuthorInterface {
    $this->set('org_name', $name);
    $this->set('org_unit', $unit);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrganizationName(): ?string {
    if (!$this->hasOrganization()) {
      return NULL;
    }
    return $this->get('org_name')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function hasOrganization(): bool {
    $has_name = !$this->get('org_name')->isEmpty();
    $has_unit = !$this->get('org_unit')->isEmpty();
    return $has_name && $has_unit;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrganizationUnit(): ?string {
    if (!$this->hasOrganization()) {
      return NULL;
    }
    return $this->get('org_unit')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function clearOrganization(): AuthorInterface {
    $this->set('org_name', NULL);
    $this->set('org_unit', NULL);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setHomepage(string $url): AuthorInterface {
    $this->set('homepage', $url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getHomepage(): ?string {
    if (!$this->hasHomepage()) {
      return NULL;
    }
    return $this->get('homepage')->getString();
  }

  /**
   * {@inheritdoc}
   */
  public function hasHomepage(): bool {
    return !$this->get('homepage')->isEmpty();
  }

  /**
   * {@inheritdoc}
   */
  public function clearHomepage(): AuthorInterface {
    $this->set('homepage', NULL);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription(array $description): AuthorInterface {
    $this->set('description', $description);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription(): ?array {
    if (!$this->hasDescription()) {
      return NULL;
    }
    return $this->get('description')->first()->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function hasDescription(): bool {
    return !$this->get('description')->isEmpty();
  }

  /**
   * {@inheritdoc}
   */
  public function clearDescription(): AuthorInterface {
    $this->set('description', NULL);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setImageMedia(MediaInterface $media): AuthorInterface {
    $this->set('image', $media);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getImageMedia(): ?MediaInterface {
    if (!$this->hasImage()) {
      return NULL;
    }
    return $this->get('image')->first()->get('entity')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function hasImage(): bool {
    return !$this->get('image')->isEmpty();
  }

  /**
   * {@inheritdoc}
   */
  public function getImageId(): ?int {
    if (!$this->hasImage()) {
      return NULL;
    }
    return (int) $this->get('image')->first()->get('target_id')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function clearImage(): AuthorInterface {
    return $this->set('image', NULL);
  }

}
