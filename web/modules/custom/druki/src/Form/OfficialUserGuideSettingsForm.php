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
  public static function create(ContainerInterface $container): object {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->responsiveImageStyleHelper = $container->get('druki.helper.responsive_image_style');
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
    $form['#tree'] = TRUE;

    $form['image'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Image'),
    ];

    $default_image = NULL;
    if ($media_id = $this->state->get(self::IMAGE_STORAGE_KEY)) {
      $media_storage = $this->entityTypeManager->getStorage('media');
      if ($media = $media_storage->load($media_id)) {
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
      '#options' => $this->responsiveImageStyleHelper->getOptions(),
      '#default_value' => $settings->get('image_style'),
      '#title' => new TranslatableMarkup('Image style'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $responsive_image_style_id = $form_state->getValue(['image', 'style']);
    if ($responsive_image_style_id) {
      $this->config('druki.official_user_guide_settings')
        ->set('image_style', $responsive_image_style_id)
        ->save();
    }

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
