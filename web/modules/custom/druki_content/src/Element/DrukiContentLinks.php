<?php

namespace Drupal\druki_content\Element;

use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides a render element to display an entity links.
 *
 * Properties:
 * - #entity: The entity object.
 *
 * Usage Example:
 *
 * @code
 * $build['links'] = [
 *   '#type' => 'druki_content_links',
 *   '#entity' => $entity,
 * ];
 * @endcode
 *
 * @RenderElement("druki_content_links")
 */
class DrukiContentLinks extends RenderElement {

  /**
   * Entity element pre render callback.
   *
   * @param array $element
   *   An associative array containing the properties of the entity element.
   *
   * @return array
   *   The modified element.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  public static function preRenderElement(array $element) {
    if (!$element['#entity'] instanceof DrukiContentInterface) {
      return [];
    }

    $entity = $element['#entity'];

    $element['wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => ['druki-content-tabs'],
      ],
    ];

    $element['wrapper']['read'] = [
      '#type' => 'link',
      '#title' => t('View'),
      '#url' => $entity->toUrl('canonical'),
      '#attributes' => [
        'rel' => 'nofollow noopener',
        'class' => [
          'druki-content-tabs__link',
          'druki-content-tabs__link--read',
        ],
      ],
    ];

    $element['wrapper']['edit'] = [
      '#type' => 'link',
      '#title' => new TranslatableMarkup('Edit'),
      '#url' => $entity->toUrl('edit-remote'),
      '#attributes' => [
        'target' => '_blank',
        'rel' => 'nofollow noopener',
        'class' => [
          'druki-content-tabs__link',
          'druki-content-tabs__link--edit',
        ],
      ],
    ];

    $element['wrapper']['history'] = [
      '#type' => 'link',
      '#title' => t('History'),
      '#url' => $entity->toUrl('history-remote'),
      '#attributes' => [
        'target' => '_blank',
        'rel' => 'nofollow noopener',
        'class' => [
          'druki-content-tabs__link',
          'druki-content-tabs__link--history',
        ],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#pre_render' => [
        [get_class($this), 'preRenderElement'],
      ],
      '#entity' => NULL,
    ];
  }

}
