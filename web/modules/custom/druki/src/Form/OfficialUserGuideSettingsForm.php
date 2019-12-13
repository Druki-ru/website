<?php

namespace Drupal\druki\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\MediaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Druki user guide settings.
 */
class OfficialUserGuideSettingsForm extends ConfigFormBase {

  /**
   * The media storage.
   *
   * @var \Drupal\media\MediaStorage
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
   * Constructs a FrontpageSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    EntityTypeManagerInterface $entity_type_manager
  ) {

    parent::__construct($config_factory);

    $this->mediaStorage = $entity_type_manager->getStorage('media');
    $this->mediaViewBuilder = $entity_type_manager->getViewBuilder('media');
    $this->responsiveImageStyleStorage = $entity_type_manager->getStorage('responsive_image_style');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): object {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
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
      '#default_value' => isset($image_settings['style']) ? $image_settings['style'] : NULL,
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
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__);

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
