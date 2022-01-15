<?php

namespace Drupal\druki_content\Element;

use Drupal\Core\Link;
use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a render element to display previous and next links.
 *
 * Properties:
 * - #prev_link: The Link to previous content.
 * - #next_link: The Link to next content.
 *
 * Usage Example:
 *
 * @code
 * $build['next_prev'] = [
 *   '#type' => 'druki_content_next_prev',
 *   '#prev_link' => $object,
 *   '#next_link' => $object,
 * ];
 * @endcode
 *
 * @RenderElement("druki_content_next_prev")
 */
class ContentNextPrev extends RenderElement {

  /**
   * Entity element pre render callback.
   *
   * @param array $element
   *   An associative array containing the properties of the entity element.
   *
   * @return array
   *   The modified element.
   */
  public static function preRenderElement(array $element): array {
    $prev_link = $element['#prev_link'];
    $next_link = $element['#next_link'];

    if (!$prev_link instanceof Link) {
      $prev_link = NULL;
    }

    if (!$next_link instanceof Link) {
      $next_link = NULL;
    }

    if (!$prev_link && !$next_link) {
      return [];
    }

    $element['#theme'] = 'druki_content_next_prev';
    $element['#prev_link'] = $prev_link;
    $element['#next_link'] = $next_link;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#pre_render' => [
        [static::class, 'preRenderElement'],
      ],
      '#prev_link' => NULL,
      '#next_link' => NULL,
    ];
  }

}
