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
   * @var \Drupal\druki_content\Handler\DrukiContentStorage
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
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
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
  public function processItem($data): void {
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
  protected function parseContent(string $filepath): array {
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
  protected function processContent(array $structured_data, array $data): void {
    $core_version = isset($structured_data['meta']['core']) ? $structured_data['meta']['core'] : NULL;
    $druki_content = $this->loadContent(
      $structured_data['meta']['id'],
      $data['langcode'],
      $core_version,
      $data['relative_pathname']
    );

    // Don't update content if last commit for source file is the same.
    if ($druki_content->get('last_commit_id') == $data['last_commit_id']) {
      return;
    }

    // Update/add everything else except ID.
    $druki_content->setTitle($structured_data['meta']['title']);
    $druki_content->setRelativePathname($data['relative_pathname']);
    $druki_content->setFilename($data['filename']);
    $druki_content->setLastCommitId($data['last_commit_id']);
    $druki_content->setContributionStatistics($data['contribution_statistics']);
    if (isset($structured_data['meta']['category-area'])) {
      $category_area = $structured_data['meta']['category-area'];
      $category_order = isset($structured_data['meta']['category-order']) ? $structured_data['meta']['category-order'] : 0;
      $druki_content->setCategory($category_area, $category_order);
    }

    if ($core_version) {
      $druki_content->setCore($core_version);
    }

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

    // @see druki_content_tokens()
    if (isset($structured_data['meta']['path'])) {
      $forced_alias = $structured_data['meta']['path'];
      $druki_content->set('forced_path', $forced_alias);
    }

    $this->processDifficulty($druki_content, $structured_data);
    $this->processLabels($druki_content, $structured_data);

    $druki_content->save();
  }

  /**
   * Checks is content with provided ID already existing.
   *
   * @param string $external_id
   *   The external content ID.
   * @param string $langcode
   *   The langcode.
   * @param string $core_version
   *   The core version.
   * @param string|null $relative_pathname
   *   The relative pathname for content.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|NULL
   */
  protected function loadContent(
    string $external_id,
    string $langcode,
    string $core_version = NULL,
    string $relative_pathname = NULL
  ): ?DrukiContentInterface {

    $druki_content = $this->drukiContentStorage->loadByMeta($external_id, $langcode, $core_version);

    if ($druki_content instanceof DrukiContentInterface) {
      return $druki_content;
    }
    else {
      // Trying to find entity by relative pathname. This covers some cases such
      // as change core version in the file.
      $druki_content = $this->drukiContentStorage->loadByRelativePathname($relative_pathname);

      if ($druki_content instanceof DrukiContentInterface) {
        return $druki_content;
      }
      else {
        return $this->drukiContentStorage->create([
          'external_id' => $external_id,
          'langcode' => $langcode,
        ]);
      }
    }
  }

  /**
   * Creates paragraphs for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The content entity.
   * @param array $structured_data
   *   The array with structured data.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphs(DrukiContentInterface $druki_content, array $structured_data): void {
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

        case 'note':
          $paragraph = $this->createParagraphNote($content_data);
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
  protected function createParagraphContent(array $content_data): ParagraphInterface {
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
  protected function createParagraphHeading(array $content_data): ParagraphInterface {
    $paragraph = $this->paragraphStorage->create(['type' => 'druki_heading']);
    $paragraph->set('druki_textfield_formatted', [
      'value' => $content_data['value'],
      'format' => filter_default_format(),
    ]);
    $paragraph->set('druki_heading_level', $content_data['level']);
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
  protected function createParagraphCode(array $content_data): ParagraphInterface {
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
  protected function createParagraphImage(array $content_data): ?ParagraphInterface {
    $host = parse_url($content_data['src']);
    $src = $content_data['src'];
    $alt = $content_data['alt'];

    // If scheme is exists, then we treat this file as remote.
    if (isset($host['scheme'])) {
      $filename = basename($src);
      $file_uri = system_retrieve_file($src, 'temporary://' . $filename);
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

  /**
   * Creates note paragraph.
   *
   * @param array $content_data
   *   The array with all data.
   *
   * @return \Drupal\paragraphs\ParagraphInterface|null
   *   The created paragraph, NULL if cant create it.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphNote(array $content_data): ?ParagraphInterface {
    $paragraph = $this->paragraphStorage->create(['type' => 'druki_note']);
    $paragraph->set('druki_note_type', $content_data['note_type']);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $content_data['value'],
      'format' => filter_default_format(),
    ]);
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Process difficulty content value into field value.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The entity to save value.
   * @param array $structured_data
   *   An array with structured data from source file.
   */
  protected function processDifficulty(DrukiContentInterface $druki_content, array $structured_data): void {
    // Reset value. Assumes that value was cleared.
    $druki_content->set('difficulty', NULL);

    if (isset($structured_data['meta']['difficulty'])) {
      // Get available values directly from field.
      $field_definitions = $this
        ->entityFieldManager
        ->getFieldDefinitions('druki_content', 'druki_content');

      if (isset($field_definitions['difficulty'])) {
        $difficulty = $field_definitions['difficulty'];
        $settings = $difficulty->getSetting('allowed_values');
        $allowed_values = array_keys($settings);

        if (in_array($structured_data['meta']['difficulty'], $allowed_values)) {
          $druki_content->set('difficulty', $structured_data['meta']['difficulty']);
        }
      }
    }
  }

  /**
   * Process labels for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The entity to save value.
   * @param array $structured_data
   *   An array with structured data from source file.
   */
  protected function processLabels(DrukiContentInterface $druki_content, array $structured_data): void {
    // Reset value. Assumes that value was cleared.
    $druki_content->set('labels', NULL);

    if (isset($structured_data['meta']['labels'])) {
      $labels = explode(', ', $structured_data['meta']['labels']);

      if (!empty($labels)) {
        $druki_content->set('labels', $labels);
      }
    }
  }

}
