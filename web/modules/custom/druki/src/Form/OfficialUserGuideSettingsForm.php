<?php

namespace Drupal\druki\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Druki user guide settings.
 */
final class OfficialUserGuideSettingsForm extends ConfigFormBase {

  /**
   * The state key for media entity storage.
   */
  public const IMAGE_STORAGE_KEY = 'druki_official_user_guide_media';

  /**
   * The media storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaStorage;

  /**
   * The responsive image style sotrage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $responsiveImageStyleStorage;

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\State
   */
  protected $state;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): object {
    $instance = new static($container->get('config.factory'));
    $instance->mediaStorage = $container->get('entity_type.manager')->getStorage('media');
    $instance->responsiveImageStyleStorage = $container->get('entity_type.manager')
      ->getStorage('responsive_image_style');
    $instance->state = $container->get('state');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_official_user_guide_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $settings = $this->config('druki.official_user_guide_settings');

    $form['image'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Image'),
      '#tree' => TRUE,
    ];

    $default_image = NULL;
    if ($media_id = $this->state->get(self::IMAGE_STORAGE_KEY)) {
      if ($media = $this->mediaStorage->load($media_id)) {
        $default_image = $media;
      }
    }

    $form['image']['media'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['image'],
      ],
      '#title' => new TranslatableMarkup('Image'),
      '#default_value' => $default_image,
    ];

    $form['image']['style'] = [
      '#type' => 'select',
      '#options' => $this->getResponsiveImageStyleOptions(),
      '#default_value' => $settings->get('image_style'),
      '#title' => new TranslatableMarkup('Image style'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Gets options for responsive styles.
   *
   * @return array
   *   The array contains responsive image styles, where key is responsive image
   *   style id, and the value is label.
   */
  protected function getResponsiveImageStyleOptions(): array {
    $result = &drupal_static(self::class . ':' . __METHOD__);

    if (isset($result)) {
      return $result;
    }

    $responsive_image_styles = $this->responsiveImageStyleStorage->loadMultiple();
    $responsive_image_style_options = [];
    foreach ($responsive_image_styles as $responsive_image_style) {
      $responsive_image_style_options[$responsive_image_style->id()] = $responsive_image_style->label();
    }

    $result = $responsive_image_style_options;

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('druki.official_user_guide_settings')
      ->set('image_style', $form_state->getValue(['image', 'style']))
      ->save();

    if ($media_id = $form_state->getValue(['image', 'media'])) {
      $this->state->set(self::IMAGE_STORAGE_KEY, $media_id);
    }
    else {
      $this->state->delete(self::IMAGE_STORAGE_KEY);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['druki.official_user_guide_settings'];
  }

}
