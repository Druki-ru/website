<?php

declare(strict_types=1);

namespace Drupal\druki_content\Builder;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\druki_content\Data\ContentElementInterface;

/**
 * Provides interface for content element render element builder.
 */
interface ContentElementRenderArrayBuilderInterface extends CacheableDependencyInterface {

  /**
   * Checks whenever this builder can build an element.
   *
   * @param \Drupal\druki_content\Data\ContentElementInterface $element
   *   The element to build.
   *
   * @return bool
   *   TRUE if suitable for building, FALSE otherwise.
   */
  public static function isApplicable(ContentElementInterface $element): bool;

  /**
   * Builds render array for element.
   *
   * @param \Drupal\druki_content\Data\ContentElementInterface $element
   *   The content element.
   * @param array $children_render_array
   *   A render array with children render array. Empty if element does not have
   *   any children.
   *
   * @return array
   *   The render array.
   */
  public function build(ContentElementInterface $element, array $children_render_array = []): array;

}
