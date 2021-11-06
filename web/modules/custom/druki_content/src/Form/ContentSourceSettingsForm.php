<?php

declare(strict_types=1);

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides form for configure content source settings.
 */
final class ContentSourceSettingsForm extends FormBase {

  /**
   * The content source settings repository.
   */
  protected ContentSourceSettingsInterface $contentSourceSettings;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    $instance = parent::create($container);
    $instance->contentSourceSettings = $container->get('druki_content.repository.content_source_settings');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_content_source_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['repository_url'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Repository URL'),
      '#description' => new TranslatableMarkup('The remote repository URL.'),
      '#attributes' => [
        'placeholder' => 'https://github.com/Druki-ru/content',
      ],
      '#default_value' => $this->contentSourceSettings->getRepositoryUrl(),
      '#required' => TRUE,
    ];

    $form['repository_uri'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('Repository URI'),
      '#description' => new TranslatableMarkup('The local repository URI.'),
      '#attributes' => [
        'placeholder' => 'public://path/to/store/repository',
      ],
      '#default_value' => $this->contentSourceSettings->getRepositoryUri(),
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
    $this->contentSourceSettings
      ->setRepositoryUri($form_state->getValue('repository_uri'))
      ->setRepositoryUrl($form_state->getValue('repository_url'));
  }

}
