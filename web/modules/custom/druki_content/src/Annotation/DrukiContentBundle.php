<?php

declare(strict_types=1);

namespace Drupal\druki_content\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the DrukiContentBundle plugin annotation.
 *
 * @Annotation
 */
final class DrukiContentBundle extends Plugin {

  /**
   * The bundle ID (type).
   */
  public string $id;

  /**
   * The bundle label.
   */
  public string $label;

}
