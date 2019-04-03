<?php

namespace Drupal\druki_media\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\media\OEmbed\ResourceFetcherInterface;
use Drupal\media\OEmbed\UrlResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Optimized remote video' formatter.
 *
 * @FieldFormatter(
 *   id = "druki_media_optimized_remote_video",
 *   label = @Translation("Optimized remote video"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class OptimizedRemoteVideoFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The image styles.
   *
   * @var array
   */
  protected $imageStyles;

  /**
   * The URL resolver.
   *
   * @var \Drupal\media\OEmbed\UrlResolverInterface
   */
  protected $urlResolver;

  /**
   * The resource fetcher.
   *
   * @var \Drupal\media\OEmbed\ResourceFetcherInterface
   */
  protected $resourceFetcher;

  /**
   * OptimizedRemoteVideoFormatter constructor.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\media\OEmbed\UrlResolverInterface $url_resolver
   *   The URL resolver.
   * @param \Drupal\media\OEmbed\ResourceFetcherInterface $resource_fetcher
   *   The resource fetcher.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    UrlResolverInterface $url_resolver,
    ResourceFetcherInterface $resource_fetcher
  ) {

    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings
    );

    $this->imageStyles = image_style_options();

    $this->urlResolver = $url_resolver;
    $this->resourceFetcher = $resource_fetcher;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('media.oembed.url_resolver'),
      $container->get('media.oembed.resource_fetcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    $image_styles = image_style_options();
    $image_style_names = array_keys($image_styles);
    $first_style = array_shift($image_style_names);

    return [
        'thumbnail_style' => $first_style,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $entity_type_id = $field_definition->getTargetEntityTypeId();
    $entity_bundle = $field_definition->getTargetBundle();

    return $entity_type_id == 'media' && $entity_bundle == 'remote_video';
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {

    $elements['thumbnail_style'] = [
      '#type' => 'select',
      '#options' => $this->imageStyles,
      '#title' => $this->t('Thumbnail style'),
      '#default_value' => $this->getSetting('thumbnail_style'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary[] = $this->t(
      'Thumbnail style: @style', [
      '@style' => $this->imageStyles[$this->getSetting('thumbnail_style')],
    ]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];

    foreach ($items as $delta => $item) {
      /** @var \Drupal\media\MediaInterface $media */
      $media = $item->getEntity();
      /** @var \Drupal\file\FileInterface $thumbnail_file */
      $thumbnail_file = $media->get('thumbnail')->entity;

      $remote_video_url = $item->value;
      $resource_url = $this->urlResolver->getResourceUrl($remote_video_url);
      $oembed_resource = $this->resourceFetcher->fetchResource($resource_url);

      if ($oembed_resource->getType() == 'video') {
        $provider = $oembed_resource->getProvider();
        $provider_name = $provider->getName();
        $provider_id = $this->parseVideoId($provider_name, $remote_video_url);

        if ($provider_id) {
          $element[$delta] = [
            '#theme' => 'druki_media_remote_video_optimized',
            '#thumbnail_uri' => $thumbnail_file->getFileUri(),
            '#thumbnail_style' => $this->getSetting('thumbnail_style'),
            '#thumbnail_alt' => $media->label(),
            '#video_provider' => $provider_name,
            '#video_id' => $provider_id,
          ];
        }
      }
    }

    return $element;
  }

  /**
   * Gets video ID from URL.
   *
   * @param string $provider_name
   *   The provider name.
   * @param string $video_url
   *   The video URL.
   *
   * @return string|null
   *   The video ID, NULL if can't be parsed.
   */
  protected function parseVideoId(string $provider_name, string $video_url): ?string {
    switch ($provider_name) {
      case 'YouTube':
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video_url, $match)) {
          return $match[1];
        }
        break;
    }

    return NULL;
  }

}
