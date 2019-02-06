<?php

namespace Drupal\druki_content\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_parser\Service\DrukiHTMLParserInterface;
use Drupal\druki_parser\Service\DrukiMarkdownParserInterface;
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
   * @param \Drupal\druki_parser\Service\DrukiMarkdownParserInterface $markdown_parser
   *   The markdown parser.
   * @param \Drupal\druki_parser\Service\DrukiHTMLParserInterface $html_parser
   *   The HTML parser.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, DrukiMarkdownParserInterface $markdown_parser, DrukiHTMLParserInterface $html_parser) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
    $this->paragraphStorage = $entity_type_manager->getStorage('paragraph');
    $this->markdownParser = $markdown_parser;
    $this->htmlParser = $html_parser;
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
      $container->get('druki_parser.markdown'),
      $container->get('druki_parser.html')
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

        case 'code':
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
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The created paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraphImage($content_data) {
    // @todo Create module to handle file duplicates and reuse them. Also, seems
    // like needed new service to help working with files inside repo, or
    // settings.

    // Detect if url is remote. Local will return NULL.
    //    $host = parse_url('src');
    //    dump($host);
  }

}
