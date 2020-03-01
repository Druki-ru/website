<?php

namespace Drupal\druki\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Url;

/**
 * Provides render element for card element.
 *
 * Inspired by https://material.io/components/cards.
 *
 * @RenderElement("druki_card")
 */
class Card extends RenderElement {

  /**
   * Additional processing for element.
   *
   * @param array $element
   *   The element.
   *
   * @return array
   *   The processed element.
   */
  public static function preRender(array $element) {
    $element['#theme'] .= '__' . $element['#variant'];

    if ($element['#primary_url'] && !$element['#primary_url'] instanceof Url) {
      if ((strpos($element['#primary_url'], '/') !== 0) && (strpos($element['#primary_url'], '#') !== 0) && (strpos($element['#primary_url'], '?') !== 0)) {
        $element['#primary_url'] = Url::fromUri($element['#primary_url']);
      }
      else {
        $element['#primary_url'] = Url::fromUserInput($element['#primary_url']);
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'druki_card',
      '#title' => NULL,
      '#subhead' => NULL,
      '#supporting_text' => NULL,
      '#buttons' => [],
      '#style' => 'elevated',
      '#variant' => 'basic',
      '#primary_url' => NULL,
      '#pre_render' => [
        [$class, 'preRender'],
      ],
    ];
  }

}
