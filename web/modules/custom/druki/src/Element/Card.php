<?php

namespace Drupal\druki\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\StringTranslation\TranslatableMarkup;
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
    if (isset($element['#primary_url']) && !$element['#primary_url'] instanceof Url) {
      if ((strpos($element['#primary_url'], '/') !== 0) && (strpos($element['#primary_url'], '#') !== 0) && (strpos($element['#primary_url'], '?') !== 0)) {
        $primary_url = Url::fromUri($element['#primary_url']);
      }
      else {
        $primary_url = Url::fromUserInput($element['#primary_url']);
      }
    }
    elseif (isset($element['#primary_url']) && $element['#primary_url'] instanceof Url) {
      $primary_url = $element['#primary_url'];
    }
    else {
      $primary_url = NULL;
    }

    if ($primary_url) {
      $primary_url->setOption('attributes', [
        'class' => 'button button--primary button--small',
      ]);

      $element['#actions'][] = [
        '#type' => 'link',
        '#title' => new TranslatableMarkup('Read more'),
        '#url' => $primary_url,
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'druki_card',
      '#title' => NULL,
      '#subhead' => NULL,
      '#description' => NULL,
      '#primary_url' => NULL,
      '#actions' => [],
      '#pre_render' => [
        [static::class, 'preRender'],
      ],
    ];
  }

}
