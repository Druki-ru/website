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

}
