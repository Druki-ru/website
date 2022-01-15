<?php

namespace Drupal\druki_content\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\druki\Data\Contributor;
use Drupal\druki\Data\ContributorList;
use Drupal\druki_content\Data\ContentDocument;

/**
 * Provides an interface defining a druki content entity type.
 */
interface ContentInterface extends ContentEntityInterface {

  /**
   * Gets the druki content title.
   *
   * @return string
   *   Title of the druki content.
   */
  public function getTitle(): string;

  /**
   * Sets the druki content title.
   *
   * @param string $title
   *   The druki content title.
   *
   * @return \Drupal\druki_content\Entity\ContentInterface
   *   The called druki content entity.
   */
  public function setTitle(string $title): ContentInterface;

  /**
   * Gets relative pathname of source file.
   *
   * @return string
   *   The relative pathname.
   */
  public function getRelativePathname(): string;

  /**
   * Sets relative pathname.
   *
   * @param string $relative_pathname
   *   The relative pathname.
   *
   * @return \Drupal\druki_content\Entity\ContentInterface
   *   The called druki content entity.
   */
  public function setRelativePathname(string $relative_pathname): ContentInterface;

  /**
   * Sets core version.
   *
   * @param int|null $core
   *   The core version.
   *
   * @return \Drupal\druki_content\Entity\ContentInterface
   *   The called druki content entity.
   */
  public function setCore(?int $core): ContentInterface;

  /**
   * Gets core version.
   *
   * @return string|null
   *   The core version, NULL if not set.
   */
  public function getCore(): ?string;

  /**
   * Sets category.
   *
   * @param string $area
   *   The category area.
   * @param int $order
   *   The order inside area.
   * @param string|null $title
   *   The custom title for category.
   *
   * @return \Drupal\druki_content\Entity\ContentInterface
   *   The called druki content entity.
   */
  public function setCategory(string $area, int $order = 0, ?string $title = NULL): ContentInterface;

  /**
   * Reset category value.
   */
  public function unsetCategory(): void;

  /**
   * Gets category.
   *
   * @return array|null
   *   The category info.
   */
  public function getCategory(): ?array;

  /**
   * Gets source hash.
   *
   * @return string|null
   *   The source hash.
   */
  public function getSourceHash(): ?string;

  /**
   * Sets source hash.
   *
   * @param string $hash
   *   The hash.
   *
   * @return $this
   */
  public function setSourceHash(string $hash): ContentInterface;

  /**
   * Gets content slug.
   *
   * @return string
   *   The content slug.
   */
  public function getSlug(): string;

  /**
   * Sets content document.
   *
   * @param \Drupal\druki_content\Data\ContentDocument $content_document
   *   The content document.
   *
   * @return $this
   */
  public function setContentDocument(ContentDocument $content_document): self;

  /**
   * Gets content document.
   *
   * @return \Drupal\druki_content\Data\ContentDocument|null
   *   The content document.
   */
  public function getContentDocument(): ?ContentDocument;

  /**
   * Resets contributors field values.
   */
  public function unsetContributors(): void;

  /**
   * Adds contributor.
   *
   * @param \Drupal\druki\Data\Contributor $contributor
   *   The contributor object.
   *
   * @return $this
   */
  public function addContributor(Contributor $contributor): self;

  /**
   * Sets contributors field value.
   *
   * @param \Drupal\druki\Data\ContributorList $contributors
   *   The contributors list.
   *
   * @return $this
   */
  public function setContributors(ContributorList $contributors): self;

  /**
   * Gets content contributors.
   *
   * @return \Drupal\druki\Data\ContributorList
   *   An object with contributors.
   */
  public function getContributors(): ContributorList;

}
