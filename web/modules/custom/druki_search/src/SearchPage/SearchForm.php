<?php

namespace Drupal\druki_search\SearchPage;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SearchForm extends FormBase {


  public function __construct(RequestStack $request_stack) {
    $this->setRequestStack($request_stack);
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  public static function afterBuild($form) {
    foreach (['form_id', 'form_build_id', 'form_token', 'op'] as $element) {
      unset($form[$element]);
    }

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function getFormId() {
    return 'druki_search_page_search';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form_state->setMethod('GET');
    $request = $this->getRequest();

    $form['text'] = [
      '#type' => 'textfield',
      '#default_value' => $request->query->get('text'),
      '#attributes' => [
        'placeholder' => new TranslatableMarkup('What are we searching for today?'),
      ],
    ];

    $form['submit'] = [
      '#type' => 'html_tag',
      '#tag' => 'button',
      '#attributes' => [
        'class' => [Html::getClass($this->getFormId() . '__submit')],
      ],
    ];

    $form['#after_build'][] = [get_class($this), 'afterBuild'];

    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
