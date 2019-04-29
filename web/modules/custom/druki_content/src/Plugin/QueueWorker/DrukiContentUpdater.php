<?php

namespace Drupal\druki_content\Plugin\QueueWorker;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Utility\Token;
use Drupal\druki_content\Common\ContentQueueItem;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_file\Service\DrukiFileTracker;
use Drupal\druki_paragraphs\Common\Content\ContentStructure;
use Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphCode;
use Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphHeading;
use Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphImage;
use Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphNote;
use Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphText;
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
 * @todo improve updating. If id changed and\or added core in meta tags, content
 * can be found but this values wont change.
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
   * The logger.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * The default format for filtered content.
   *
   * @var string
   */
  protected $filterDefaultFormat;

  /**
   * The state storage.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

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
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state storage.
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
    Token $token,
    LoggerChannelInterface $logger,
    StateInterface $state
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
    $this->logger = $logger;
    $this->state = $state;
    $this->filterDefaultFormat = 'markdown';
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
      $container->get('token'),
      $container->get('logger.factory')->get('druki_content'),
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   *
   * @see DrukiContentSubscriber::onPullFinish().
   */
  public function processItem($queue_item): void {
    // First of all, check is item is value object we expected. We ignore all
    // values not passed via our object.
    if (!$queue_item instanceof ContentQueueItem) {
      $this->logger->error('Queue got unexpected item value. @debug', [
        '@debug' => '<pre><code>' . print_r($queue_item, TRUE) . '</code></pre>',
      ]);

      return;
    }

    $structured_data = $this->parseContent($queue_item->getPath());

    // Skip processing for invalid data.
    if (!$structured_data->valid()) {
      $this->logger->error('The processing of file "@filepath" skipped, because structured content is not valid. @dump', [
        '@filepath' => $queue_item->getPath(),
        '@dump' => '<pre><code>' . print_r($structured_data, TRUE) . '</code></pre>',
      ]);
      return;
    }

    $this->processContent($structured_data, $queue_item);
  }

  /**
   * Parses content from Markdown file to structured array.
   *
   * @param string $filepath
   *   The URI to file with content.
   *
   * @return ContentStructure
   *   The structured content.
   *
   * @see Drupal\druki_parser\Service\DrukiHTMLParser::parse().
   */
  protected function parseContent(string $filepath): ContentStructure {
    $content = file_get_contents($filepath);
    $content_html = $this->markdownParser->parse($content);

    return $this->htmlParser->parse($content_html);
  }

  /**
   * Creates or updates druki content entity.
   *
   * @param \Drupal\druki_paragraphs\Common\Content\ContentStructure $structured_data
   *   The structured content.
   * @param \Drupal\druki_content\Common\ContentQueueItem $queue_item
   *   The queue item object.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function processContent(ContentStructure $structured_data, ContentQueueItem $queue_item): void {
    $meta = $structured_data->getMetaInformation();

    $druki_content = $this->loadContent(
      $meta->get('id')->getValue(),
      $queue_item->getLangcode(),
      $queue_item->getRelativePathname()
    );

    // Don't update content if last commit for source file is the same.
    $is_same_commit = ($druki_content->get('last_commit_id')->value == $queue_item->getLastCommitId());
    // If force update is set in settings. Ignore rule above.
    $force_update = $this->state->get('druki_content.settings.force_update', FALSE);
    if ($is_same_commit && !$force_update) {
      return;
    }

    // Update/add everything else except ID.
    $druki_content->setTitle($meta->get('title')->getValue());
    $druki_content->setRelativePathname($queue_item->getRelativePathname());
    $druki_content->setFilename($queue_item->getFilename());
    $druki_content->setLastCommitId($queue_item->getLastCommitId());
    $druki_content->setContributionStatistics($queue_item->getContributionStatistics());

    if ($meta->has('category-area')) {
      $category_area = $meta->get('category-area')->getValue();
      $category_order = $meta->has('category-order') ? $meta->get('category-order')->getValue() : 0;
      $category_title = $meta->has('category-title') ? $meta->get('category-title')->getValue() : NULL;

      $druki_content->setCategory($category_area, $category_order, $category_title);
    }

    if ($meta->has('core')) {
      $druki_content->setCore($meta->get('core')->getValue());
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
    if ($meta->has('path')) {
      $forced_alias = $meta->get('path')->getValue();
      $druki_content->set('forced_path', $forced_alias);
    }

    $this->processDifficulty($druki_content, $structured_data);
    $this->processLabels($druki_content, $structured_data);
    $this->processSearchKeywords($druki_content, $structured_data);
    $this->processMetatags($druki_content, $structured_data);

    $druki_content->save();
  }

  /**
   * Checks is content with provided ID already existing.
   *
   * @param string $external_id
   *   The external content ID.
   * @param string $langcode
   *   The langcode.
   * @param string|null $relative_pathname
   *   The relative pathname for content.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|NULL
   */
  protected function loadContent(
    string $external_id,
    string $langcode,
    string $relative_pathname = NULL
  ): ?DrukiContentInterface {

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

  /**
   * Creates paragraphs for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The content entity.
   * @param \Drupal\druki_paragraphs\Common\Content\ContentStructure $structured_data
   *   The structured data.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphs(DrukiContentInterface $druki_content, ContentStructure $structured_data): void {
    foreach ($structured_data->getContent() as $content) {
      $paragraph = NULL;

      switch ($content->getParagraphType()) {
        case 'druki_text':
          $paragraph = $this->createParagraphContent($content);
          break;

        case 'druki_heading':
          $paragraph = $this->createParagraphHeading($content);
          break;

        case 'druki_code':
          $paragraph = $this->createParagraphCode($content);
          break;

        case 'druki_image':
          $paragraph = $this->createParagraphImage($content);
          break;

        case 'druki_note':
          $paragraph = $this->createParagraphNote($content);
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
   * @param \Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphText $text
   *   The text object.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphContent(ParagraphText $text): ParagraphInterface {
    $paragraph = $this->paragraphStorage->create(['type' => $text->getParagraphType()]);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $text->getContent(),
      'format' => $this->filterDefaultFormat,
    ]);
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates heading paragraph.
   *
   * @param \Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphHeading $heading
   *   The heading object.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphHeading(ParagraphHeading $heading): ParagraphInterface {
    $paragraph = $this->paragraphStorage->create(['type' => $heading->getParagraphType()]);
    $paragraph->set('druki_textfield_formatted', [
      'value' => $heading->getContent(),
      'format' => $this->filterDefaultFormat,
    ]);
    $paragraph->set('druki_heading_level', $heading->getLevel());
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates code paragraph.
   *
   * @param \Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphCode $code
   *   The code object..
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphCode(ParagraphCode $code): ParagraphInterface {
    $paragraph = $this->paragraphStorage->create(['type' => $code->getParagraphType()]);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $code->getContent(),
      'format' => 'full_html',
    ]);
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates image paragraph.
   *
   * @param \Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphImage $image
   *   The image object.
   *
   * @return \Drupal\paragraphs\ParagraphInterface|null
   *   The created paragraph, NULL if cant create it.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphImage(ParagraphImage $image): ?ParagraphInterface {
    $src = $image->getSrc();
    $alt = $image->getAlt();
    $host = parse_url($src);

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
      $paragraph = $this->paragraphStorage->create(['type' => $image->getParagraphType()]);
      $duplicate = $this->fileTracker->checkDuplicate($file_uri);

      // If we already have file with same content.
      if ($duplicate instanceof FileInterface) {
        $media = $this->saveImageFileToMediaImage($duplicate, $alt);
      }
      else {
        $destination_uri = $this->getMediaImageFieldDestination();
        $basename = basename($file_uri);
        $data = file_get_contents($file_uri);

        // Ensure folder is exists and writable.
        if (file_prepare_directory($destination_uri, FILE_CREATE_DIRECTORY)) {
          $file = file_save_data($data, $destination_uri . '/' . $basename);
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
        $paragraph->save();

        return $paragraph;
      }
    }

    return NULL;
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
   * @param \Drupal\druki_paragraphs\Common\ParagraphContent\ParagraphNote $note
   *   The note object.
   *
   * @return \Drupal\paragraphs\ParagraphInterface|null
   *   The created paragraph, NULL if cant create it.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphNote(ParagraphNote $note): ?ParagraphInterface {
    $paragraph = $this->paragraphStorage->create(['type' => $note->getParagraphType()]);
    $paragraph->set('druki_note_type', $note->getType());
    $paragraph->set('druki_textarea_formatted', [
      'value' => $note->getContent(),
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
   * @param \Drupal\druki_paragraphs\Common\Content\ContentStructure $structured_data
   *   The content structure.
   */
  protected function processDifficulty(DrukiContentInterface $druki_content, ContentStructure $structured_data): void {
    // Reset value. Assumes that value was cleared.
    $druki_content->set('difficulty', NULL);
    $meta = $structured_data->getMetaInformation();

    if ($meta->has('difficulty')) {
      // Get available values directly from field.
      $field_definitions = $this
        ->entityFieldManager
        ->getFieldDefinitions('druki_content', 'druki_content');

      if (isset($field_definitions['difficulty'])) {
        $difficulty = $field_definitions['difficulty'];
        $settings = $difficulty->getSetting('allowed_values');
        $allowed_values = array_keys($settings);

        if (in_array($meta->get('difficulty')->getValue(), $allowed_values)) {
          $druki_content->set('difficulty', $meta->get('difficulty')->getValue());
        }
      }
    }
  }

  /**
   * Process labels for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The entity to save value.
   * @param \Drupal\druki_paragraphs\Common\Content\ContentStructure $structured_data
   *   The content structure.
   */
  protected function processLabels(DrukiContentInterface $druki_content, ContentStructure $structured_data): void {
    // Reset value. Assumes that value was cleared.
    $druki_content->set('labels', NULL);
    $meta = $structured_data->getMetaInformation();

    if ($meta->has('labels')) {
      $druki_content->set('labels', $meta->get('labels')->getValue());
    }
  }

  /**
   * Process search keywords for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The entity to save value.
   * @param \Drupal\druki_paragraphs\Common\Content\ContentStructure $structured_data
   *   The content structure.
   */
  protected function processSearchKeywords(DrukiContentInterface $druki_content, ContentStructure $structured_data): void {
    // Reset value. Assumes that value was cleared.
    $druki_content->set('search_keywords', NULL);
    $meta = $structured_data->getMetaInformation();

    if ($meta->has('search-keywords')) {
      $druki_content->set('search_keywords', $meta->get('search-keywords')->getValue());
    }
  }

  /**
   * Process metatags for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The entity to save value.
   * @param \Drupal\druki_paragraphs\Common\Content\ContentStructure $structured_data
   *   The content structure.
   */
  protected function processMetatags(DrukiContentInterface $druki_content, ContentStructure $structured_data): void {
    $druki_content->set('metatags', NULL);
    $meta = $structured_data->getMetaInformation();

    if ($meta->has('metatags')) {
      $metatags = $meta->get('metatags')->getValue();
      $allowed_values = ['title', 'description'];

      foreach ($metatags as $key => $value) {
        if (!in_array($key, $allowed_values)) {
          unset($metatags[$key]);
        }
      }

      if (isset($metatags['title'])) {
        $metatags['og_title'] = $metatags['title'];
        $metatags['twitter_cards_title'] = $metatags['title'];
      }

      if (isset($metatags['description'])) {
        $metatags['og_description'] = $metatags['description'];
      }

      $druki_content->set('metatags', serialize($metatags));
    }
  }

}
