<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class FilterForm extends FormBase {

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'druki_search_page_filter';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['test'] = [
      '#type' => 'textfield',
      '#title' => 'test',
    ];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
