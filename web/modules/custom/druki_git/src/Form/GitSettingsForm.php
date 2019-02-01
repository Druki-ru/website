<?php

namespace Drupal\druki_git\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\druki_git\Service\GitInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Druki — git settings for this site.
 */
class GitSettingsForm extends ConfigFormBase {

  use StringTranslationTrait;
  use MessengerTrait;

  /**
   * The git helper.
   *
   * @var \Drupal\druki_git\Service\GitInterface
   */
  protected $git;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, GitInterface $git) {
    parent::__construct($config_factory);

    $this->git = $git;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('druki.git')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'druki_git_git_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    if (!$repository_status = $this->fetchRepositoryInfo()) {
      $message = $this->t('Git repository not found in provided path. Please, make sure you have actual repository in the path, or path is correct.');

      $this->messenger()->addError($message);
    }
    else {
      $form['git'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Git status'),
      ];

      $status_markup = implode('<br/>', $repository_status);
      $form['git']['status'] = [
        '#markup' => $status_markup,
      ];

      $form['git']['actions']['#type'] = 'actions';
      $form['git']['actions']['pull'] = [
        '#type' => 'submit',
        '#value' => $this->t('Pull from remote repository'),
        '#submit' => [[$this, 'gitPullFromRemote']],
      ];
    }

    $form['repository_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Repository path'),
      '#description' => $this->t('The local path to repository cloning.'),
      '#attributes' => [
        'placeholder' => 'public://path/to/store/repository',
      ],
      '#default_value' => $this->config('druki_git.git_settings')
        ->get('repository_path'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Gets repository status info.
   *
   * @return array|null
   *   An array with status information or NULL, if repository not found.
   */
  public function fetchRepositoryInfo() {
    if ($this->git->init()) {
      $messages = [];
      $messages[] = $this->t('<strong>Last commit ID:</strong> @value', ['@value' => $this->git->getLastCommitId()]);

      return $messages;
    }
  }

  /**
   * Pulls actual repository data from remote.
   */
  public function gitPullFromRemote() {
    if ($this->git->pull()) {
      $this->messenger()->addStatus($this->t('Git pull executed successully.'));
    }
    else {
      $this->messenger()->addError($this->t('Git pull end up with error.'));
    }
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
      ->set('repository_path', $form_state->getValue('repository_path'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['druki_git.git_settings'];
  }

}
