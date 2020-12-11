<?php

namespace Drupal\druki\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\MediaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Druki user guide settings.
 */
final class OfficialUserGuideSettingsForm extends ConfigFormBase {

  /**
   * The media storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaStorage;

  /**
   * The media view builder.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilderInterface
   */
  protected $mediaViewBuilder;

  /**
   * The responsive image style sotrage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $responsiveImageStyleStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): object {
    $instance = new static($container->get('config.factory'));
    $instance->mediaStorage = $container->get('entity_type.manager')->getStorage('media');
    $instance->mediaViewBuilder = $container->get('entity_type.manager')->getViewBuilder('media');
    $instance->responsiveImageStyleStorage = $container->get('entity_type.manager')->getStorage('responsive_image_style');

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
    $image_settings = $this->config('druki.official_user_guide_settings')->get('image');

    $form['image'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Image'),
      '#tree' => TRUE,
    ];

    $default_image = NULL;
    if (isset($image_settings['media'])) {
      $media = $this->mediaStorage->load($image_settings['media']);

      if ($media instanceof MediaInterface) {
        $default_image = $media;

        $preview = $this->mediaViewBuilder->view($default_image, 'media_library');
        $preview['#prefix'] = '<div class="media-library-item">';
        $preview['#suffix'] = '</div>';
        $form['image']['image_preview'] = $preview;
        $form['#attached']['library'][] = 'media_library/style';
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
      '#default_value' => isset($image_settings['style']) ?? $image_settings['style'],
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
      ->set('image', $form_state->getValue('image'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['druki.official_user_guide_settings'];
  }

}
