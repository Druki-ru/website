<?php

namespace Drupal\druki_content\SourceContent;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\ParsedContent\Content\ContentList;
use Drupal\druki_content\ParsedContent\Content\ParagraphCode;
use Drupal\druki_content\ParsedContent\Content\ParagraphHeading;
use Drupal\druki_content\ParsedContent\Content\ParagraphImage;
use Drupal\druki_content\ParsedContent\Content\ParagraphNote;
use Drupal\druki_content\ParsedContent\Content\ParagraphText;
use Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Provides class to load parsed content into "druki_content" entity.
 *
 * This class will take all care about finding the existed entity or creating
 * new one, as wel as processing all necessary values.
 *
 * @todo Think how processing can be decoupled. E.g. we can create manager and
 *   dedicated processors for Front Matter and Paragraphs.
 */
final class ParsedSourceContentLoader {

  /**
   * The druki content storage.
   *
   * @var \Drupal\druki_content\Handler\DrukiContentStorage
   */
  protected $drukiContentStorage;

  /**
   * The system time.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The paragraph storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $paragraphStorage;

  /**
   * Constructs a new ParsedSourceContentLoader object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The system time.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TimeInterface $time) {
    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
    $this->paragraphStorage = $entity_type_manager->getStorage('paragraph');
    $this->time = $time;
  }

  /**
   * Process parsed content.
   *
   * @param \Drupal\druki_content\SourceContent\ParsedSourceContent $parsed_source_content
   *   The parsed source content.
   * @param bool $force
   *   TRUE if content must be updated eve if source hash is equal.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The update content entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function process(ParsedSourceContent $parsed_source_content, bool $force = FALSE): DrukiContentInterface {
    $druki_content = $this->loadDrukiContent($parsed_source_content);
    $current_source_hash = $parsed_source_content->getSourceHash();
    $same_source_hash = $druki_content->getSourceHash() == $current_source_hash;
    // Update content if source_hash are different or force param set to TRUE.
    if (!$same_source_hash || $force) {
      $parsed_source = $parsed_source_content->getParsedSource();
      $druki_content->setRelativePathname($parsed_source_content->getSource()->getRelativePathname());
      $druki_content->setSourceHash($parsed_source_content->getSourceHash());
      $this->processFrontMatter($druki_content, $parsed_source->getFrontMatter());
      $this->processContent($druki_content, $parsed_source->getContent());
    }

    $druki_content->setSyncTimestamp($this->time->getCurrentTime());
    $druki_content->save();

    return $druki_content;
  }

  /**
   * Loads druki_content entity to work with.
   *
   * If entity is not found, new one will be created.
   *
   * @param \Drupal\druki_content\SourceContent\ParsedSourceContent $parsed_source_content
   *   The parsed source content.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface
   *   The druki content entity.
   */
  protected function loadDrukiContent(ParsedSourceContent $parsed_source_content): DrukiContentInterface {
    $front_matter = $parsed_source_content->getParsedSource()->getFrontMatter();
    $id = $front_matter->get('id')->getValue();
    $core = NULL;
    if ($front_matter->has('core')) {
      $core = $front_matter->get('core')->getValue();
    }
    $language = $parsed_source_content->getSource()->getLanguage();
    $entity = $this->drukiContentStorage->loadByExternalId($id, $language, $core);

    if (!$entity) {
      $entity = $this->drukiContentStorage->create([
        'external_id' => $id,
        'langcode' => $language,
      ]);
    }

    return $entity;
  }

  /**
   * Process Front Matter values.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   * @param \Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter $front_matter
   *   The front matter values.
   */
  protected function processFrontMatter(DrukiContentInterface $druki_content, FrontMatter $front_matter): void {
    $druki_content->setTitle($front_matter->get('title')->getValue());

    if ($front_matter->has('category')) {
      $category = $front_matter->get('category')->getValue();
      $category_area = $category['area'];
      $category_order = (isset($category['order'])) ? $category['order'] : 0;
      $category_title = (isset($category['title'])) ? $category['title'] : NULL;

      $druki_content->setCategory($category_area, $category_order, $category_title);
    }

    if ($front_matter->has('core')) {
      $druki_content->setCore($front_matter->get('core')->getValue());
    }

    // @see druki_content_tokens()
    if ($front_matter->has('path')) {
      $forced_alias = $front_matter->get('path')->getValue();
      $druki_content->set('forced_path', $forced_alias);
    }

    // Reset value. Assumes that value was cleared.
    // @todo Consider remove this field or use it.
    $druki_content->set('difficulty', NULL);
    if ($front_matter->has('difficulty')) {
      // Get available values directly from field.
      $field_definitions = $druki_content->getFieldDefinitions();

      if (isset($field_definitions['difficulty'])) {
        $difficulty = $field_definitions['difficulty'];
        $settings = $difficulty->getSetting('allowed_values');
        $allowed_values = array_keys($settings);

        if (in_array($front_matter->get('difficulty')->getValue(), $allowed_values)) {
          $druki_content->set('difficulty', $front_matter->get('difficulty')->getValue());
        }
      }
    }

    // Reset value. Assumes that value was cleared.
    // @todo Consider remove this field or use it.
    $druki_content->set('labels', NULL);
    if ($front_matter->has('labels')) {
      $druki_content->set('labels', $front_matter->get('labels')->getValue());
    }

    // Reset value. Assumes that value was cleared.
    $druki_content->set('search_keywords', NULL);
    if ($front_matter->has('search-keywords')) {
      $druki_content->set('search_keywords', $front_matter->get('search-keywords')->getValue());
    }

    // Reset value. Assumes that value was cleared.
    $druki_content->set('metatags', NULL);
    if ($front_matter->has('metatags')) {
      $metatags = $front_matter->get('metatags')->getValue();
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

  /**
   * Process content values.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   * @param \Drupal\druki_content\ParsedContent\Content\ContentList $content_list
   *   The parsed content from source.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function processContent(DrukiContentInterface $druki_content, ContentList $content_list): void {
    $this->deleteParagraphs($druki_content);
    $this->createParagraphs($druki_content, $content_list);
  }

  /**
   * Delete all paragraphs with content.
   *
   * If this content already contains paragraphs, we delete them. It's faster
   * and safer to recreate it from new structure, other than detecting
   * changes. Maybe in the future it will be improved, but not in experiment.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function deleteParagraphs(DrukiContentInterface $druki_content): void {
    if ($druki_content->get('content')->isEmpty()) {
      return;
    }

    $paragraphs = $druki_content->get('content')->referencedEntities();
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    foreach ($paragraphs as $paragraph) {
      $paragraph->delete();
    }

    $druki_content->set('content', NULL);
  }

  /**
   * Creates paragraphs for content.
   *
   * @param \Drupal\druki_content\Entity\DrukiContentInterface $druki_content
   *   The druki content.
   * @param \Drupal\druki_content\ParsedContent\Content\ContentList $content_list
   *   The parsed content from source.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphs(DrukiContentInterface $druki_content, ContentList $content_list): void {
    /** @var \Drupal\druki_content\ParsedContent\Content\ParagraphContentInterface $content */
    foreach ($content_list as $content) {
      $paragraph = NULL;

      switch ($content->getParagraphType()) {
        case 'druki_text':
          $paragraph = $this->createParagraphText($content);
          break;

        case 'druki_heading':
          $paragraph = $this->createParagraphHeading($content);
          break;

        case 'druki_code':
          $paragraph = $this->createParagraphCode($content);
          break;

        case 'druki_image':
          // @todo refactor.
//          $paragraph = $this->createParagraphImage($content);
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
   * Creates text paragraph.
   *
   * @param \Drupal\druki_content\ParsedContent\Content\ParagraphText $text
   *   The text object.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphText(ParagraphText $text): ParagraphInterface {
    $paragraph = $this->paragraphStorage->create(['type' => $text->getParagraphType()]);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $text->getContent(),
      // Use full html for default form since we convert markdown during sync.
      // Using markdown filter will only reduce performance for nothing.
      'format' => 'full_html',
    ]);
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates heading paragraph.
   *
   * @param \Drupal\druki_content\ParsedContent\Content\ParagraphHeading $heading
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
      'format' => 'full_html',
    ]);
    $paragraph->set('druki_heading_level', $heading->getLevel());
    $paragraph->save();

    return $paragraph;
  }

  /**
   * Creates code paragraph.
   *
   * @param \Drupal\druki_content\ParsedContent\Content\ParagraphCode $code
   *   The code object.
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
   * @param \Drupal\druki_content\ParsedContent\Content\ParagraphImage $image
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
        // @todo replace deprecated.
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
   * @param \Drupal\druki_content\ParsedContent\Content\ParagraphNote $note
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
      'format' => 'full_html',
    ]);
    $paragraph->save();

    return $paragraph;
  }

}
