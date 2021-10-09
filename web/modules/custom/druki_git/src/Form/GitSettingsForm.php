<?php

namespace Drupal\druki_git\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_git\Exception\GitCommandFailedException;
use Drupal\druki_git\Git\GitInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Configure Druki — git settings for this site.
 */
final class GitSettingsForm extends ConfigFormBase {

  use StringTranslationTrait;
  use MessengerTrait;

  /**
   * The git helper.
   */
  protected GitInterface $git;

  /**
   * The state system.
   */
  protected StateInterface $state;

  /**
   * The logger.
   */
  protected LoggerChannelInterface $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): object {
    $instance = parent::create($container);
    $instance->git = $container->get('druki_git');
    $instance->state = $container->get('state');
    $instance->logger = $container->get('logger.channel.druki_git');
    $instance->setRequestStack($container->get('request_stack'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_git_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    if (!$repository_status = $this->fetchRepositoryInfo()) {
      $message = $this->t('Git repository not found in provided path. Please, make sure you have actual repository in the path, or path is correct.');

      $this->messenger()->addError($message);
    }
    else {
      $form['git'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Git status'),
      ];

      $status_markup = \implode('<br/>', $repository_status);
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
    $webhook_url = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . '/api/webhook/' . $webhook_key;

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

    $form['repository'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Repository settings'),
    ];

    $form['repository']['repository_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Repository URL'),
      '#description' => $this->t('The remote repository URL.'),
      '#attributes' => [
        'placeholder' => 'https://github.com/Niklan/druki-content',
      ],
      '#default_value' => $this
        ->config('druki_git.git_settings')
        ->get('repository_url'),
      '#required' => TRUE,
    ];

    $form['repository']['repository_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Repository path'),
      '#description' => $this->t('The local path to repository cloning.'),
      '#attributes' => [
        'placeholder' => 'public://path/to/store/repository',
      ],
      '#default_value' => $this
        ->config('druki_git.git_settings')
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
  public function fetchRepositoryInfo(): ?array {
    try {
      $messages = [];
      $messages[] = $this->t('<strong>Last commit ID:</strong> @value', ['@value' => $this->git->getLastCommitId()]);

      return $messages;
    }
    catch (GitCommandFailedException $e) {
      return NULL;
    }
  }

  /**
   * Pulls actual repository data from remote.
   */
  public function gitPullFromRemote(): void {
    $this->logger->info('Manually requested git pull.');

    if ($this->git->pull()) {
      $this->messenger()->addStatus($this->t('Git pull executed successfully.'));
    }
    else {
      $this->messenger()->addError($this->t('Git pull end up with error.'));
    }
  }

  /**
   * Regenerates key for webhook url.
   */
  public function regenerateWebhookKey(): void {
    $webhook_key = Crypt::randomBytesBase64(55);
    $this->state->set('druki_git.webhook_key', $webhook_key);
    $this->logger->notice('The webhook key was regenerated manually to "@webhook_key".', [
      '@webhook_key' => $webhook_key,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('druki_git.git_settings')
      ->set('repository_path', $form_state->getValue('repository_path'))
      ->set('repository_url', $form_state->getValue('repository_url'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['druki_git.git_settings'];
  }

}
