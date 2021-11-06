<?php

declare(strict_types=1);

namespace Drupal\druki_content\Form;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\druki_content\Repository\ContentWebhookSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides form for content webhook settings.
 */
final class ContentWebhookSettingsForm extends FormBase {

  /**
   * The content webhook settings repository.
   */
  protected ContentWebhookSettingsInterface $contentWebhookSettings;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = parent::create($container);
    $instance->contentWebhookSettings = $container->get('druki_content.repository.content_webhook_settings');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_content_webhook_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['update'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Content update'),
    ];

    $form['update']['description'] = [
      '#markup' => new TranslatableMarkup('This webhook is used to update content from remote and rebuild it. This webhook only accepts POST requests.'),
    ];

    $form['update']['url'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Webhook URL'),
      '#attributes' => [
        'readonly' => TRUE,
      ],
      '#value' => Url::fromRoute(
        'druki_content.webhook.update',
        ['access_key' => $this->contentWebhookSettings->getContentUpdateWebhookAccessKey()],
      )->setAbsolute()->toString(),
    ];

    $form['update']['actions']['#type'] = 'actions';
    $form['update']['actions']['regenerate'] = [
      '#type' => 'submit',
      '#value' => new TranslatableMarkup('Regenerate URL'),
      '#button_type' => 'danger',
      '#name' => 'regenerate_update_key',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $triggered_element_name = $form_state->getTriggeringElement()['#name'];
    if ($triggered_element_name == 'regenerate_update_key') {
      $this->contentWebhookSettings->setContentUpdateWebhookAccessKey(Crypt::randomBytesBase64(55));
      $status_message = new TranslatableMarkup("The content update Webhook URL is regenerated. Don't forget to update this URL everywhere it was used.");
      $this->messenger()->addStatus($status_message);
    }
  }

}
