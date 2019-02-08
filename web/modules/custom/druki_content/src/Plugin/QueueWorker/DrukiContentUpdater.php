<?php

namespace Drupal\druki_content\Plugin\QueueWorker;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\Utility\Token;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_file\Service\DrukiFileTracker;
use Drupal\druki_parser\Service\DrukiHTMLParserInterface;
use Drupal\druki_parser\Service\DrukiMarkdownParserInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\paragraphs\ParagraphInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @QueueWorker(
 *   id = "druki_content_updater",
 *   title = @Translation("Druki content updater."),
 *   cron = {"time" = 60}
 * )
 */
class DrukiContentUpdater extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * The druki content storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $drukiContentStorage;

  /**
   * The markdown parser.
   *
   * @var \Drupal\druki_parser\Service\DrukiMarkdownParserInterface
   */
  protected $markdownParser;

  /**
   * The HTML parser.
   *
   * @var \Drupal\druki_parser\Service\DrukiHTMLParserInterface
   */
  protected $htmlParser;

  /**
   * The paragraph storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $paragraphStorage;

  /**
   * The file tracker.
   *
   * @var \Drupal\druki_file\Service\DrukiFileTracker
   */
  protected $fileTracker;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The media storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaStorage;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The token.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * DrukiContentUpdater constructor.
   *
   * @param array $configuration
   *   The configuration.
   * @param $plugin_id
   *   The plugin id.
   * @param $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\druki_parser\Service\DrukiMarkdownParserInterface $markdown_parser
   *   The markdown parser.
   * @param \Drupal\druki_parser\Service\DrukiHTMLParserInterface $html_parser
   *   The HTML parser.
   * @param \Drupal\druki_file\Service\DrukiFileTracker $file_tracker
   *   The file tracker.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Utility\Token $token
   *   The token.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    EntityFieldManagerInterface $entity_field_manager,
    DrukiMarkdownParserInterface $markdown_parser,
    DrukiHTMLParserInterface $html_parser,
    DrukiFileTracker $file_tracker,
    ConfigFactoryInterface $config_factory,
    Token $token
  ) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
    $this->paragraphStorage = $entity_type_manager->getStorage('paragraph');
    $this->mediaStorage = $entity_type_manager->getStorage('media');
    $this->entityFieldManager = $entity_field_manager;
    $this->markdownParser = $markdown_parser;
    $this->htmlParser = $html_parser;
    $this->fileTracker = $file_tracker;
    $this->configFactory = $config_factory;
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('druki_parser.markdown'),
      $container->get('druki_parser.html'),
      $container->get('druki_file.tracker'),
      $container->get('config.factory'),
      $container->get('token')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @see DrukiContentSubscriber::onPullFinish().
   */
  public function processItem($data) {
    $structured_data = $this->parseContent($data['path']);
    $this->processContent($structured_data, $data);
  }

  /**
   * Parses content from Markdown file to structured array.
   *
   * @param string $filepath
   *   The URI to file with content.
   *
   * @return array
   *   The array containing structured array with all data.
   *
   * @see Drupal\druki_parser\Service\DrukiHTMLParser::parse().
   */
  protected function parseContent($filepath) {
    $content = file_get_contents($filepath);
    $content_html = $this->markdownParser->parse($content);

    return $this->htmlParser->parse($content_html);
  }

  /**
   * Creates or updates druki content entity.
   *
   * @param array $structured_data
   *   The array with structured data.
   * @param array $data
   *   The data passed to Queue. In our case this is additional file info.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function processContent($structured_data, $data) {
    $druki_content = $this->loadContent($structured_data['meta']['id']);

    // Update/add everything else except ID.
    $druki_content->set('title', $structured_data['meta']['title']);
    $druki_content->set('relative_pathname', $data['relative_pathname']);
    $druki_content->set('filename', $data['filename']);
    $druki_content->set('last_commit_id', $data['last_commit_id']);
    // @todo contribution_statistics

    // If this content already contains paragraphs, we delete them. It's faster
    // and safer to recreate it from new structure, other than detecting
    // changes. Maybe in the future it will be improved, but not in experiment.
    if (!$druki_content->get('content')->isEmpty()) {
      $paragraphs = $druki_content->get('content')->referencedEntities();
      /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
      foreach ($paragraphs as $paragraph) {
        $paragraph->delete();
      }

      $druki_content->set('content', NULL);
    }

    $this->createParagraphs($druki_content, $structured_data);
    $druki_content->save();
  }

  /**
   * Checks is content with provided ID already existing.
   *
   * @param string $id
   *   The content ID.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|NULL
   */
  protected function loadContent($id) {
    $druki_content = $this->drukiContentStorage->load($id);

    if ($druki_content instanceof DrukiContentInterface) {
      return $druki_content;
    }
    else {
      return $this->drukiContentStorage->create(['id' => $id]);
    }
  }

  /**
   * Creates paragraphs for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The content entity.
   * @param $structured_data
   *   The array with structured data.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphs(DrukiContentInterface $druki_content, $structured_data) {
    foreach ($structured_data['content'] as $content_data) {
      $paragraph = NULL;
      switch ($content_data['type']) {
        case 'content':
          $paragraph = $this->createParagraphContent($content_data);
          break;

        case 'heading':
          $paragraph = $this->createParagraphHeading($content_data);
          break;

        case 'code':
          $paragraph = $this->createParagraphCode($content_data);
          break;

        case 'image':
          $paragraph = $this->createParagraphImage($content_data);
          break;
      }

      if ($paragraph instanceof ParagraphInterface) {
        $druki_content->get('content')->appendItem([
          'target_id' => $paragraph->id(),
          'target_revision_id' => $paragraph->getRevisionId(),
        ]);
      }
    }
  }

  /**
   * Creates content paragraph.
   *
   * @param array $content_data
   *   The array with all data.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphContent($content_data) {
    $paragraph = $this->paragraphStorage->create(['type' => 'druki_text']);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $content_data['markup'],
      'format' => filter_default_format(),
    ]);
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates heading paragraph.
   *
   * @param array $content_data
   *   The array with all data.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphHeading($content_data) {
    $paragraph = $this->paragraphStorage->create(['type' => 'druki_heading']);
    $paragraph->set('druki_textfield_formatted', [
      'value' => $content_data['value'],
      'format' => filter_default_format(),
    ]);
    // @todo looks like this is not working.
    $paragraph->set('druki_heading_type', $content_data['level']);
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates code paragraph.
   *
   * @param array $content_data
   *   The array with all data.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphCode($content_data) {
    $paragraph = $this->paragraphStorage->create(['type' => 'druki_code']);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $content_data['value'],
      'format' => filter_default_format(),
    ]);
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates image paragraph.
   *
   * @param array $content_data
   *   The array with all data.
   *
   * @return \Drupal\paragraphs\ParagraphInterface|null
   *   The created paragraph, NULL if cant create it.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphImage($content_data) {
    $host = parse_url($content_data['src']);
    $src = $content_data['src'];
    $alt = $content_data['alt'];
    $file_uri = '';

    // If scheme is exists, then we treat this file as remote.
    if (isset($host['scheme'])) {
      $filename = basename($src);
      $destination_uri = $this->getMediaImageFieldDestination();
      $file = system_retrieve_file($src, $destination_uri . '/' . $filename, TRUE);

      if ($file instanceof FileInterface) {
        $file_uri = $file->getFileUri();
      }
    }
    else {
      // If no scheme is set, we treat this file as local and relative to
      // repository root folder.
      $repository_path = $this->configFactory->get('druki_git.git_settings')
        ->get('repository_path');
      $repository_path = rtrim($repository_path, '/');
      $src = ltrim($src, '/');
      $file_uri = $repository_path . '/' . $src;
    }

    // If file is found locally.
    if (file_exists($file_uri)) {
      $paragraph = $this->paragraphStorage->create(['type' => 'druki_image']);
      $duplicate = $this->fileTracker->checkDuplicate($file_uri);

      // If we already have file with same content.
      if ($duplicate instanceof FileInterface) {
        $media = $this->saveImageFileToMediaImage($duplicate, $alt);
      }
      else {
        $destination_uri = $this->getMediaImageFieldDestination();
        $basename = basename($file_uri);
        $data = file_get_contents($file_uri);
        /** @var \Drupal\file\FileInterface $file */
        $file = file_save_data($data, $destination_uri . '/' . $basename);
        $media = $this->saveImageFileToMediaImage($file);
      }

      // If media entity was created or found.
      if ($media instanceof MediaInterface) {
        $paragraph->set('druki_image', [
          'target_id' => $media->id(),
        ]);
        $paragraph->save();

        return $paragraph;
      }
    }
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
  protected function getMediaImageFieldDestination() {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__);

    if (!isset($destination)) {
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

  /**
   * Saves or return currently existed media image entity for file.
   *
   * @param \Drupal\file\FileInterface $file
   *   The file entity.
   * @param string|null $name
   *   The name for media entity, if it creates.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Drupal\media\MediaInterface
   *   The created or found media entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function saveImageFileToMediaImage(FileInterface $file, $name = NULL) {
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

}