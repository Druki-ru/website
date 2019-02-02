<?php

namespace Drupal\druki_parser\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\markdown\Markdown;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Object for parse markdown and html and transform to specific data structures.
 *
 * @package Drupal\druki_parser\Service
 */
class DrukiParser implements DrukiParserInterface {

  /**
   * The markdown parser.
   *
   * @var \Drupal\markdown\Plugin\Markdown\MarkdownParserInterface
   */
  protected $markdownParser;

  /**
   * DrukiParser constructor.
   *
   * @param \Drupal\markdown\Markdown $markdown
   *   The markdown service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(Markdown $markdown, EntityTypeManagerInterface $entity_type_manager) {
    $user_storage = $entity_type_manager->getStorage('user');
    $user = $user_storage->load(1);
    // The markdown looking for filters available for provided user. If we call
    // it via Drush, we will be anonymous user, and if filter is not accessible
    // to him, markdown will be converted without extensions. So we force in
    // code to handle it via admin user.
    $this->markdownParser = $markdown->getParser('thephpleague/commonmark', 'markdown', $user);
  }

  /**
   * Parses markdown content.
   *
   * @param string $content
   *   The markdown content.
   *
   * @return string
   *   The HTML markup.
   */
  public function parseMarkdown($content) {
    return $this->markdownParser->convertToHtml($content);
  }

  /**
   * Parses HTML to structured data.
   *
   * @param string $content
   *   The html content.
   *
   * @return array
   *   An array with structured data.
   */
  public function parseHtml($content) {
    $crawler = new Crawler($content);
    // Move to body. We expect content here.
    $crawler = $crawler->filter('body');
    // For now we have this structure types:
    // - heading: Heading elements.
    // - content: Almost every content, p, ul, li, span and so on.
    // - image: Image tag.
    // - code: for pre > code.
    // Each content node after another merge to previous.
    $structure = [];
    // Move through elements and structure them.
    foreach ($crawler->children() as $dom_element) {
      if ($this->isHeading($dom_element->nodeName)) {
        $structure[] = [
          'type' => 'heading',
          'heading' => $dom_element->nodeName,
          'value' => $dom_element->textContent,
        ];

        continue;
      }

      if ($this->isCode($dom_element->nodeName)) {
        $structure[] = [
          'type' => 'code',
          'value' => $dom_element->ownerDocument->saveHTML($dom_element),
        ];

        continue;
      }

      if ($image_info = $this->isImage($dom_element)) {
        $structure[] = [
          'type' => 'image',
          'src' => $image_info[0][0],
          'alt' => $image_info[0][1],
        ];
      }

      // If no other is detected, treat is as content.
    }

    dump($structure);
  }

  protected function isHeading($node_name) {
    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    return in_array($node_name, $heading_elements);
  }

  protected function isCode($node_name) {
    $content_elements = ['pre'];

    return in_array($node_name, $content_elements);
  }

  protected function isImage($dom_element) {
    $crawler = new Crawler($dom_element);
    $image = $crawler->filter('img')->extract(['src', 'alt']);
    if (!empty($image)) {
      return $image;
    }
  }

}
