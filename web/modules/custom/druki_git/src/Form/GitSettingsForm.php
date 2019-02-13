<?php

namespace Drupal\druki_git\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_git\Service\GitInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
   * The state system.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The current request.
   *
   * @var \Symfony\Component\BrowserKit\Request
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    GitInterface $git,
    StateInterface $state,
    Request $request
  ) {

    parent::__construct($config_factory);

    $this->git = $git;
    $this->state = $state;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('druki_git'),
      $container->get('state'),
      $container->get('request_stack')->getCurrentRequest()
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

    $form['webhook'] = [
      '#type' => 'fieldset',
      '#title' => 'Webhook',
    ];

    $webhook_key = $this->state->get('druki_git.webhook_key');
    $webhook_url = $this->request->getSchemeAndHttpHost() . '/api/webhook/' . $webhook_key;

    $webhook_description = '<p>' . $this->t('The Webhook URL which can be used to trigger pull events and all consecutive operations.') . '</p>';
    $webhook_description .= '<p>' . new TranslatableMarkup('The Webhook URL: <a href=":url">:url</a>', [
        ':url' => $webhook_url,
      ]);
    $form['webhook']['description'] = [
      '#markup' => $webhook_description,
    ];

    $form['webhook']['actions']['#type'] = 'actions';
    $form['webhook']['actions']['regenerate'] = [
      '#type' => 'submit',
      '#value' => $this->t('Regenerate key'),
      '#submit' => [[$this, 'regenerateWebhookKey']],
    ];

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
   * Regenerates key for webhook url.
   */
  public function regenerateWebhookKey() {
    $webhook_key = Crypt::randomBytesBase64(55);
    $this->state->set('druki_git.webhook_key', $webhook_key);
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
