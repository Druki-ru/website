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
class DrukiContentNextPrev extends RenderElement {

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

    $element['wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['druki-content-next-prev'],
      ],
    ];

    $element['wrapper']['prev_wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['druki-content-next-prev__item', 'druki-content-next-prev__item--prev'],
      ],
    ];

    if ($prev_link) {
      $element['wrapper']['prev_wrapper']['link'] = $prev_link->toRenderable();
      $element['wrapper']['prev_wrapper']['link']['#attributes'] = [
        'class' => [
          'druki-content-next-prev__link',
          'druki-content-next-prev__link--prev',
        ],
      ];
    }

    $element['wrapper']['next_wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['druki-content-next-prev__item', 'druki-content-next-prev__item--next'],
      ],
    ];

    if ($next_link) {
      $element['wrapper']['next_wrapper']['link'] = $next_link->toRenderable();
      $element['wrapper']['next_wrapper']['link']['#attributes'] = [
        'class' => [
          'druki-content-next-prev__link',
          'druki-content-next-prev__link--next',
        ],
      ];
    }

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
