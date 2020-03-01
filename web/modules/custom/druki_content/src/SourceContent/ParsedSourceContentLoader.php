<?php

namespace Drupal\druki_content\SourceContent;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\ParsedContent\FrontMatter\FrontMatter;

/**
 * Provides class to load parsed content into "druki_content" entity.
 *
 * This class will take all care about finding the existed entity or creating
 * new one, as wel as processing all necessary values.
 */
final class ParsedSourceContentLoader {

  /**
   * The druki content storage.
   *
   * @var \Drupal\druki_content\Handler\DrukiContentStorage
   */
  protected $drukiContentStorage;

  /**
   * Constructs a new ParsedSourceContentLoader object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
  }

  /**
   * Process parsed content.
   *
   * @param \Drupal\druki_content\SourceContent\ParsedSourceContent $parsed_source_content
   *   The parsed source content.
   */
  public function process(ParsedSourceContent $parsed_source_content): void {
    $druki_content = $this->loadDrukiContent($parsed_source_content);
    $this->processFrontMatter($druki_content, $parsed_source_content->getParsedSource()->getFrontMatter());
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
  protected function processFrontMatter(DrukiContentInterface $druki_content, FrontMatter $front_matter) {
    dump($front_matter);
  }

}
