<?php

namespace Drupal\druki\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\MediaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Druki settings for this site.
 */
class FrontpageSettingsForm extends ConfigFormBase {

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
    return 'druki_frontpage_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $this->buildPromoSettings($form, $form_state);
    $this->buildWhyDrupalSettings($form, $form_state);
    $this->buildDrupalEventSettings($form, $form_state);

    return parent::buildForm($form, $form_state);
  }

  /**
   * Build promo settings area.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected function buildPromoSettings(array &$form, FormStateInterface $form_state): void {
    $promo_settings = $this->config('druki.frontpage_settings')->get('promo');

    $form['promo'] = [
      '#type' => 'fieldset',
      '#title' => t('Promo'),
      '#tree' => TRUE,
    ];

    $default_image = NULL;
    if (isset($promo_settings['image'])) {
      $media = $this->mediaStorage->load($promo_settings['image']);

      if ($media instanceof MediaInterface) {
        $default_image = $media;

        $preview = $this->mediaViewBuilder->view($default_image, 'media_library');
        $preview['#prefix'] = '<div class="media-library-item">';
        $preview['#suffix'] = '</div>';
        $form['promo']['image_preview'] = $preview;
        $form['#attached']['library'][] = 'media_library/style';
      }
    }

    $form['promo']['image'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['image'],
      ],
      '#title' => t('Promo image'),
      '#description' => t('Media entity that contains a promo image.'),
      '#default_value' => $default_image,
    ];

    $form['promo']['style'] = [
      '#type' => 'select',
      '#options' => $this->getResponsiveImageStyleOptions(),
      '#default_value' => isset($promo_settings['style']) ? $promo_settings['style'] : NULL,
      '#title' => t('Promo image style'),
    ];
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
   * Build "why" settings area.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected function buildWhyDrupalSettings(array &$form, FormStateInterface $form_state): void {
    $promo_settings = $this->config('druki.frontpage_settings')->get('why');

    $form['why'] = [
      '#type' => 'fieldset',
      '#title' => t('Why Drupal'),
      '#tree' => TRUE,
    ];

    $default_video = NULL;
    if (isset($promo_settings['video'])) {
      $media = $this->mediaStorage->load($promo_settings['video']);

      if ($media instanceof MediaInterface) {
        $default_video = $media;

        $preview = $this->mediaViewBuilder->view($default_video, 'media_library');
        $preview['#prefix'] = '<div class="media-library-item">';
        $preview['#suffix'] = '</div>';
        $form['why']['video_preview'] = $preview;
        $form['#attached']['library'][] = 'media_library/style';
      }
    }

    $form['why']['video'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['remote_video'],
      ],
      '#title' => t('Remote video'),
      '#description' => t('Media entity that contains a remote video.'),
      '#default_value' => $default_video,
    ];

    // @todo remove code duplication with above part.
    $default_video = NULL;
    if (isset($promo_settings['video'])) {
      $media = $this->mediaStorage->load($promo_settings['video']);

      if ($media instanceof MediaInterface) {
        $default_video = $media;

        $preview = $this->mediaViewBuilder->view($default_video, 'media_library');
        $preview['#prefix'] = '<div class="media-library-item">';
        $preview['#suffix'] = '</div>';
        $form['why']['video_preview'] = $preview;
        $form['#attached']['library'][] = 'media_library/style';
      }
    }
  }

  /**
   * Builds settings for drupal event promo.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  protected function buildDrupalEventSettings(array &$form, FormStateInterface $form_state): void {
    $event_settings = $this->config('druki.frontpage_settings')->get('event');

    $form['event'] = [
      '#type' => 'fieldset',
      '#title' => t('Drupal event'),
      '#tree' => TRUE,
    ];

    $form['event']['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => isset($event_settings['status']) ? $event_settings['status'] : FALSE,
    ];

    $default_image = NULL;
    if (isset($event_settings['image'])) {
      $media = $this->mediaStorage->load($event_settings['image']);

      if ($media instanceof MediaInterface) {
        $default_image = $media;

        $preview = $this->mediaViewBuilder->view($default_image, 'media_library');
        $preview['#prefix'] = '<div class="media-library-item">';
        $preview['#suffix'] = '</div>';
        $form['event']['image_preview'] = $preview;
        $form['#attached']['library'][] = 'media_library/style';
      }
    }

    $form['event']['image'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'media',
      '#selection_settings' => [
        'target_bundles' => ['image'],
      ],
      '#title' => t('Event image'),
      '#description' => t('Media entity that contains an event promo image.'),
      '#default_value' => $default_image,
    ];

    $form['event']['style'] = [
      '#type' => 'select',
      '#options' => $this->getResponsiveImageStyleOptions(),
      '#default_value' => isset($event_settings['style']) ? $event_settings['style'] : NULL,
      '#title' => t('Promo image style'),
    ];

    $form['event']['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#default_value' => isset($event_settings['url']) ? $event_settings['url'] : '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('druki.frontpage_settings')
      ->set('promo', $form_state->getValue('promo'))
      ->set('why', $form_state->getValue('why'))
      ->set('event', $form_state->getValue('event'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['druki.frontpage_settings'];
  }

}
