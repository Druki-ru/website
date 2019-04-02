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
 *
 * @todo dont forget to add default config + schema for it.
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
  }

  /**
   * {@inheritDoc}
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
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('druki.frontpage_settings')
      ->set('promo', $form_state->getValue('promo'))
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
