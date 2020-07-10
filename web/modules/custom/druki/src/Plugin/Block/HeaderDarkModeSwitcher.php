<?php

namespace Drupal\druki\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a header dark mode switcher block.
 *
 * @Block(
 *   id = "druki_header_dark_mode_switcher",
 *   admin_label = @Translation("Header dark mode switcher"),
 *   category = @Translation("Druki")
 * )
 */
final class HeaderDarkModeSwitcher extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'class' => 'header-dark-mode-switcher',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $form['class'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('CSS class'),
      '#required' => TRUE,
      '#default_value' => $this->configuration['class'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['class'] = $form_state->getValue('class');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $class = $this->configuration['class'];

    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'button',
      '#attributes' => [
        'class' => [
          'js-dark-mode-switcher',
          $class,
        ],
        'aria-label' => new TranslatableMarkup('Switch between dark and light styles'),
      ],
      '#attached' => [
        'library' => [
          'druki/dark-mode',
        ],
      ],
    ];

    return $build;
  }

}
