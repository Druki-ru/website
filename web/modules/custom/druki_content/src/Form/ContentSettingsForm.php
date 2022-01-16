<?php

namespace Drupal\druki_content\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\druki_content\Repository\ContentSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for a druki content entity type.
 */
final class ContentSettingsForm extends FormBase {

  /**
   * The content settings repository.
   */
  protected ContentSettingsInterface $contentSettings;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = parent::create($container);
    $instance->contentSettings = $container->get('druki_content.repository.content_settings');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_content_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#title'] = new TranslatableMarkup('Content entity settings');
    $form['#tree'] = TRUE;

    $form['git'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Git settings'),
    ];

    $form['git']['repository_url'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Repository URL'),
      '#description' => new TranslatableMarkup('The remote repository URL. Will be used for generating link for external editing and so on.'),
      '#attributes' => [
        'placeholder' => 'https://github.com/Druki-ru/content',
      ],
      '#default_value' => $this->contentSettings->getRepositoryUrl(),
      '#required' => TRUE,
    ];

    $form['git']['webhook'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Webhook settings'),
    ];

    $webhook_access_key = $this->contentSettings->getContentUpdateWebhookAccessKey();
    if ($webhook_access_key) {
      $webhook_url = Url::fromRoute(
        'druki_content.webhook.update',
        ['access_key' => $this->contentSettings->getContentUpdateWebhookAccessKey()],
      )->setAbsolute()->toString();

      $form['git']['webhook']['url'] = [
        '#type' => 'textfield',
        '#title' => new TranslatableMarkup('Webhook URL'),
        '#attributes' => [
          'readonly' => TRUE,
        ],
        '#value' => $webhook_url,
      ];
    }
    else {
      $form['git']['webhook']['url'] = [
        '#theme' => 'status_messages',
        '#message_list' => [
          'warning' => [
            new TranslatableMarkup('Webhook access key is not generated. Please generate it to use this feature.'),
          ],
        ],
        '#status_headings' => [
          'status' => new TranslatableMarkup('Status message'),
          'error' => new TranslatableMarkup('Error message'),
          'warning' => new TranslatableMarkup('Warning message'),
        ],
      ];
    }

    $form['git']['webhook']['actions']['#type'] = 'actions';
    $form['git']['webhook']['actions']['regenerate'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Regenerate URL'),
      '#button_type' => 'danger',
      '#name' => 'regenerate_update_key',
    ];

    $form['content_source'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Content source settings'),
    ];

    $form['content_source']['uri'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Repository URI'),
      '#description' => new TranslatableMarkup('The local repository URI. This URI will be used to build content from. Can be accessed via <strong>content-source://</strong> wrapper.'),
      '#attributes' => [
        'placeholder' => 'public://path/to/store/repository',
      ],
      '#default_value' => $this->contentSettings->getContentSourceUri(),
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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $triggered_element_name = $form_state->getTriggeringElement()['#name'];
    if ($triggered_element_name == 'regenerate_update_key') {
      $this->contentSettings->setContentUpdateWebhookAccessKey(Crypt::randomBytesBase64(55));
      $status_message = new TranslatableMarkup("The content update Webhook URL is regenerated. Don't forget to update this URL everywhere it was used.");
      $this->messenger()->addStatus($status_message);
      return;
    }

    $this->contentSettings
      ->setContentSourceUri($form_state->getValue(['content_source', 'uri']))
      ->setRepositoryUrl($form_state->getValue(['git', 'repository_url']));
  }

}
