<?php

namespace Drupal\druki_content\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a druki content entity type.
 */
interface DrukiContentInterface extends ContentEntityInterface {

  /**
   * Gets the druki content title.
   *
   * @return string
   *   Title of the druki content.
   */
  public function getTitle();

  /**
   * Sets the druki content title.
   *
   * @param string $title
   *   The druki content title.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setTitle($title);

  /**
   * Gets relative pathname of source file.
   *
   * @return string
   *   The relative pathname.
   */
  public function getRelativePathname();

  /**
   * Sets relative pathname
   *
   * @param string $relative_pathname
   *   The relative pathname.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setRelativePathname($relative_pathname);

  /**
   * Gets filename of source file.
   *
   * @return string
   *   The filename.
   */
  public function getFilename();

  /**
   * Sets filename of source file.
   *
   * @param string $filename
   *   The filename.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setFilename($filename);

  /**
   * Gets last commit id of source file.
   *
   * @return string
   *   The commit id.
   */
  public function getLastCommitId();

  /**
   * Sets last commit id of source file.
   *
   * @param string $commit_id
   *   The commit id.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setLastCommitId($commit_id);

  /**
   * Gets contribution statistics.
   *
   * @return array
   *   The contribution statistics.
   */
  public function getContributionStatistics();

  /**
   * Sets contribution statistics.
   *
   * @param array $contribution_statistics
   *   The contribution statistics.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setContributionStatistics(array $contribution_statistics);

  /**
   * Gets content external ID.
   *
   * @return string
   *   The external ID.
   */
  public function getExternalId();

  /**
   * Sets core version.
   *
   * @param string $core
   *   The core version.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setCore($core);

  /**
   * Gets core version.
   *
   * @return string|null
   *   The core version, NULL if not set.
   */
  public function getCore();

  /**
   * Sets TOC.
   *
   * @param string $area
   *   The TOC area.
   * @param int $order
   *   The order inside area.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The called druki content entity.
   */
  public function setTOC($area, $order = 0);

  /**
   * Gets TOC.
   *
   * @return array
   *   The TOC info.
   */
  public function getTOC();

}
