<?php

declare(strict_types=1);

namespace Drupal\druki_author\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\media\MediaInterface;

/**
 * Provides an interface for author entity.
 */
interface AuthorInterface extends ContentEntityInterface {

  /**
   * Sets unique author ID.
   *
   * @param string $id
   *   The author ID.
   *
   * @return $this
   */
  public function setId(string $id): self;

  /**
   * Sets author name.
   *
   * @param string $given
   *   The given name.
   * @param string $family
   *   The family name.
   *
   * @return $this
   */
  public function setName(string $given, string $family): self;

  /**
   * Gets author given name.
   *
   * @return string
   *   The given name.
   */
  public function getNameGiven(): string;

  /**
   * Gets author family name.
   *
   * @return string
   *   The family name.
   */
  public function getNameFamily(): string;

  /**
   * Sets author country.
   *
   * @param string $country
   *   The country ISO 3166-1 alpha-2 code.
   *
   * @return $this
   */
  public function setCountry(string $country): self;

  /**
   * Gets author country.
   *
   * @return string
   *   The country code.
   */
  public function getCountry(): string;

  /**
   * Checks if author has organization set.
   *
   * @return bool
   *   Indicates is organization info set or not.
   */
  public function hasOrganization(): bool;

  /**
   * Sets organization information.
   *
   * @param string $name
   *   The organization name.
   * @param string $unit
   *   The organization unit.
   *
   * @return $this
   */
  public function setOrganization(string $name, string $unit): self;

  /**
   * Gets organization name.
   *
   * @return string|null
   *   The organization name.
   */
  public function getOrganizationName(): ?string;

  /**
   * Gets organization unit.
   *
   * @return string|null
   *   The organization unit.
   */
  public function getOrganizationUnit(): ?string;

  /**
   * Clears organization value.
   *
   * @return $this
   */
  public function clearOrganization(): self;

  /**
   * Sets author homepage.
   *
   * @param string $url
   *   The homepage URL.
   *
   * @return $this
   */
  public function setHomepage(string $url): self;

  /**
   * Checks if author has homepage set.
   *
   * @return bool
   *   Indicates that author has homepage or not.
   */
  public function hasHomepage(): bool;

  /**
   * Gets author homepage URL.
   *
   * @return string|null
   *   The homepage URL.
   */
  public function getHomepage(): ?string;

  /**
   * Clears homepage value.
   *
   * @return $this
   */
  public function clearHomepage(): self;

  /**
   * Sets author description.
   *
   * @param array $description
   *   An array with descriptions keyed by langcode.
   *
   * @return $this
   */
  public function setDescription(array $description): self;

  /**
   * Checks if author has description set.
   *
   * @return bool
   *   Indicates that author has description or not.
   */
  public function hasDescription(): bool;

  /**
   * Gets author description.
   *
   * @return array|null
   *   An array with descriptions keyed by langcode.
   */
  public function getDescription(): ?array;

  /**
   * Clears description value.
   *
   * @return $this
   */
  public function clearDescription(): self;

  /**
   * Sets author image media.
   *
   * @param \Drupal\media\MediaInterface $media
   *   The media entity representing author image.
   *
   * @return $this
   */
  public function setImageMedia(MediaInterface $media): self;

  /**
   * Checks if author has image set.
   *
   * @return bool
   *   Indicates that author has image or not.
   */
  public function hasImage(): bool;

  /**
   * Gets author media image entity.
   *
   * @return \Drupal\media\MediaInterface|null
   *   The media entity.
   */
  public function getImageMedia(): ?MediaInterface;

  /**
   * Gets author media image ID.
   *
   * @return int|null
   *   The media image ID.
   */
  public function getImageId(): ?int;

  /**
   * Clears image value.
   *
   * @return $this
   */
  public function clearImage(): self;

}
