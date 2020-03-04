<?php

namespace Drupal\druki_content\ParsedContent\Content;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Utility\Token;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_file\Service\DrukiFileTracker;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;

/**
 * Provides loader for paragraph entity 'druki_image'.
 */
final class ParagraphImageLoader extends ParagraphLoaderBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = ParagraphImage::class;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The file tracker.
   *
   * @var \Drupal\druki_file\Service\DrukiFileTracker
   */
  protected $fileTracker;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The token converter.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The media storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaStorage;

  /**
   * Constructs a new ParagraphImageLoader object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\druki_file\Service\DrukiFileTracker $file_tracker
   *   The file tracker.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Utility\Token $token
   *   The token converter.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, DrukiFileTracker $file_tracker, EntityFieldManagerInterface $entity_field_manager, Token $token, FileSystemInterface $file_system) {
    parent::__construct($entity_type_manager);
    $this->mediaStorage = $entity_type_manager->getStorage('media');
    $this->configFactory = $config_factory;
    $this->fileTracker = $file_tracker;
    $this->entityFieldManager = $entity_field_manager;
    $this->token = $token;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public function process($data, DrukiContentInterface $content): void {
    $src = $data->getSrc();
    $alt = $data->getAlt();
    $host = parse_url($src);

    // If scheme is exists, then we treat this file as remote.
    if (isset($host['scheme'])) {
      $filename = basename($src);
      try {
        // Sometimes url can be broken, slow or not accessible at this time.
        // The cURL will throw exception, and we softly skip it.
        // @todo some kind of logging to inform about such kind of problem
        //   links.
        $file_uri = system_retrieve_file($src, 'temporary://' . $filename);
      }
      catch (\Exception $e) {
        return;
      }
    }
    else {
      // If no scheme is set, we treat this file as local and relative to
      // repository root folder.
      $repository_path = $this
        ->configFactory
        ->get('druki_git.git_settings')
        ->get('repository_path');
      $repository_path = rtrim($repository_path, '/');
      $src = ltrim($src, '/');
      $file_uri = $repository_path . '/' . $src;
    }

    // If file is found locally.
    if (file_exists($file_uri)) {
      $paragraph = $this->getParagraphStorage()->create(['type' => $data->getParagraphType()]);
      $duplicate = $this->fileTracker->checkDuplicate($file_uri);

      // If we already have file with same content.
      if ($duplicate instanceof FileInterface) {
        $media = $this->saveImageFileToMediaImage($duplicate, $alt);
      }
      else {
        $destination_uri = $this->getMediaImageFieldDestination();
        $basename = basename($file_uri);
        $contents = file_get_contents($file_uri);

        // Ensure folder is exists and writable.
        if ($this->fileSystem->prepareDirectory($destination_uri, FileSystemInterface::CREATE_DIRECTORY)) {
          $file = $this->fileSystem->saveData($contents, $destination_uri . '/' . $basename);
          if ($file instanceof FileInterface) {
            $media = $this->saveImageFileToMediaImage($file);
          }
        }
      }

      // If media entity was created or found.
      if ($media instanceof MediaInterface) {
        $paragraph->set('druki_image', [
          'target_id' => $media->id(),
        ]);
        $this->saveAndAppend($paragraph, $content);
      }
    }
  }

  /**
   * Saves or return currently existed media image entity for file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   * @param string|null $name
   *   The name for media entity, if it creates.
   *
   * @return \Drupal\media\MediaInterface
   *   The created or found media entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function saveImageFileToMediaImage(FileInterface $file, ?string $name = NULL): MediaInterface {
    /** @var \Drupal\media\MediaInterface $media */
    $media = $this->fileTracker->getMediaForFile($file);

    // If media file for this file not found, we create it.
    if (!$media instanceof MediaInterface) {
      $media = $this->mediaStorage->create(['bundle' => 'image']);
      if ($name) {
        $media->setName($name);
      }
      else {
        $media->setName($file->getFilename());
      }
      $media->set('field_media_image', [
        'target_id' => $file->id(),
      ]);
      $media->save();
    }

    return $media;
  }

  /**
   * Gets field destination value.
   *
   * Looking for value from field settings for image filed of media image
   * bundle. We will respect this setting for using same paths for all image
   * files, not matter, uploaded them programmatically or manually.
   *
   * @return string
   *   The URI folder for saving file.
   */
  protected function getMediaImageFieldDestination(): string {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__);

    if (!isset($result)) {
      $media_image = $this->entityFieldManager->getFieldDefinitions('media', 'image');
      $file_directory = trim($media_image['field_media_image']->getSetting('file_directory'), '/');
      $uri_scheme = $media_image['field_media_image']->getSetting('uri_scheme');
      // Since this setting can, and will be contain tokens by default. We must
      // handle it too. Also, tokens can contain html, so we strip it.
      $destination = PlainTextOutput::renderFromHtml($this->token->replace($file_directory));

      $result = $uri_scheme . '://' . $destination;
    }

    return $result;
  }

}
