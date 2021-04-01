<?php

namespace Drupal\druki\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides settings form for frontpage.
 */
final class FrontpageSettings extends ConfigFormBase {

  /**
   * The state key for download media image.
   */
  public const DOWNLOAD_MEDIA_STORAGE_KEY = 'druki_frontpage_download_media';

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\State
   */
  protected $state;

  /**
   * The responsive image style helper.
   *
   * @var \Drupal\druki\Helper\ResponsiveImageStyleHelper
   */
  protected $responsiveImageStyleHelper;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->responsiveImageStyleHelper = $container->get('druki.helper.responsive_image_style');
    $instance->state = $container->get('state');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;
    $settings = $this->config('druki.frontpage.settings');

    $form['download'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Download'),
    ];

    $download_media_default_media = NULL;
    if ($download_image_media_id = $this->state->get(self::DOWNLOAD_MEDIA_STORAGE_KEY)) {
      $media_storage = $this->entityTypeManager->getStorage('media');
      if ($download_media = $media_storage->load($download_image_media_id)) {
        $download_media_default_media = $download_media;
      }
    }

    $form['download']['media'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['image'],
      ],
      '#title' => new TranslatableMarkup('Image'),
      '#default_value' => $download_media_default_media,
    ];

    $form['download']['image_style'] = [
      '#type' => 'select',
      '#options' => $this->responsiveImageStyleHelper->getOptions(),
      '#default_value' => $settings->get('download.image_style'),
      '#title' => new TranslatableMarkup('Image style'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $download_media_image_style_id = $form_state->getValue(['download', 'image_style']);
    if ($download_media_image_style_id) {
      $this->config('druki.frontpage.settings')
        ->set('download.image_style', $download_media_image_style_id)
        ->save();
    }

    if ($download_media_id = $form_state->getValue(['download', 'media'])) {
      $this->state->set(self::DOWNLOAD_MEDIA_STORAGE_KEY, $download_media_id);
    }
    else {
      $this->state->delete(self::DOWNLOAD_MEDIA_STORAGE_KEY);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_frontpage_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['druki.frontpage.settings'];
  }

}
