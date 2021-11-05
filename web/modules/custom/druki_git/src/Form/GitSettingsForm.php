<?php

namespace Drupal\druki_git\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_git\Exception\GitCommandFailedException;
use Drupal\druki_git\Git\GitInterface;
use Drupal\druki_git\Repository\GitSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Druki — git settings for this site.
 */
final class GitSettingsForm extends FormBase {

  /**
   * The git helper.
   */
  protected GitInterface $git;

  /**
   * The git settings.
   */
  protected GitSettingsInterface $gitSettings;

  /**
   * The logger.
   */
  protected LoggerChannelInterface $logger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = parent::create($container);
    $instance->git = $container->get('druki_git');
    $instance->gitSettings = $container->get('druki_git.settings');
    $instance->logger = $container->get('logger.channel.druki_git');
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
      $message = new TranslatableMarkup('Git repository not found in provided path. Please, make sure you have actual repository in the path, or path is correct.');
      $this->messenger()->addError($message);
    }
    else {
      $form['git'] = [
        '#type' => 'fieldset',
        '#title' => new TranslatableMarkup('Git status'),
      ];

      $status_markup = \implode('<br/>', $repository_status);
      $form['git']['status'] = [
        '#markup' => $status_markup,
      ];

      $form['git']['actions']['#type'] = 'actions';
      $form['git']['actions']['pull'] = [
        '#type' => 'submit',
        '#value' => new TranslatableMarkup('Pull from remote repository'),
        '#submit' => [[$this, 'gitPullFromRemote']],
      ];
    }

    $form['webhook'] = [
      '#type' => 'fieldset',
      '#title' => 'Webhook',
    ];

    $webhook_key = $this->gitSettings->getWebhookAccessKey();
    $webhook_url = $this->getRequest()->getSchemeAndHttpHost() . '/api/webhook/' . $webhook_key;

    $webhook_description = '<p>' . new TranslatableMarkup('The Webhook URL which can be used to trigger pull events and all consecutive operations.') . '</p>';
    $webhook_description .= '<p>' . new TranslatableMarkup('The Webhook URL: <a href=":url">:url</a>', [
      ':url' => $webhook_url,
    ]);
    $form['webhook']['description'] = [
      '#markup' => $webhook_description,
    ];

    $form['webhook']['actions']['#type'] = 'actions';
    $form['webhook']['actions']['regenerate'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Regenerate key'),
      '#submit' => [[$this, 'regenerateWebhookKey']],
    ];

    $form['repository'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Repository settings'),
    ];

    $form['repository']['repository_url'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Repository URL'),
      '#description' => new TranslatableMarkup('The remote repository URL.'),
      '#attributes' => [
        'placeholder' => 'https://github.com/Niklan/druki-content',
      ],
      '#default_value' => $this->gitSettings->getRepositoryUrl(),
      '#required' => TRUE,
    ];

    $form['repository']['repository_path'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Repository path'),
      '#description' => new TranslatableMarkup('The local path to repository cloning.'),
      '#attributes' => [
        'placeholder' => 'public://path/to/store/repository',
      ],
      '#default_value' => $this->gitSettings->getRepositoryPath(),
      '#required' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
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
      $messages[] = new TranslatableMarkup('<strong>Last commit ID:</strong> @value', ['@value' => $this->git->getLastCommitId()]);

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
      $this->messenger()->addStatus(new TranslatableMarkup('Git pull executed successfully.'));
    }
    else {
      $this->messenger()->addError(new TranslatableMarkup('Git pull end up with error.'));
    }
  }

  /**
   * Regenerates key for webhook url.
   */
  public function regenerateWebhookKey(): void {
    $webhook_key = Crypt::randomBytesBase64(55);
    $this->gitSettings->setWebhookAccessKey($webhook_key);
    $this->logger->notice('The webhook key was regenerated manually to "@webhook_key".', [
      '@webhook_key' => $webhook_key,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->gitSettings
      ->setRepositoryPath($form_state->getValue('repository_path'))
      ->setRepositoryUrl($form_state->getValue('repository_url'));
  }

}
