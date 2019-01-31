<?php

namespace Drupal\druki_git\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Druki — git settings for this site.
 */
class GitSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'druki_git_git_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['druki_git.git_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['repository_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Repository URL'),
      '#description' => $this->t('Repository with content.'),
      '#attributes' => [
        'placeholder' => 'https://gitlab.com/Username/repository-name.git',
      ],
      '#default_value' => $this->config('druki_git.git_settings')->get('repository_url'),
      '#required' => TRUE,
    ];

    $form['repository_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Repository path'),
      '#description' => $this->t('The local path to repository cloning.'),
      '#attributes' => [
        'placeholder' => 'public://path/to/store/repository',
      ],
      '#default_value' => $this->config('druki_git.git_settings')->get('repository_path'),
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('druki_git.git_settings')
      ->set('repository_url', $form_state->getValue('repository_url'))
      ->set('repository_path', $form_state->getValue('repository_path'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
