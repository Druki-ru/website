<?php

namespace Drupal\druki_toc\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'druki_toc_widget' field widget.
 *
 * @FieldWidget(
 *   id = "druki_toc_widget",
 *   label = @Translation("TOC widget"),
 *   field_types = {"druki_toc"},
 * )
 */
class TocWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element['area'] = $element + [
      '#type' => 'textfield',
      '#default_value' => isset($items[$delta]->area) ? $items[$delta]->area : NULL,
    ];

    $element['order'] = $element + [
      '#type' => 'number',
      '#default_value' => isset($items[$delta]->order) ? $items[$delta]->order : NULL,
    ];

    return $element;
  }

}
