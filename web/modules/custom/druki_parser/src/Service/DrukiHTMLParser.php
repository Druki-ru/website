<?php

namespace Drupal\druki_parser\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\markdown\Markdown;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Parser for HTML.
 *
 * @package Drupal\druki_parser\Service
 */
class DrukiHTMLParser implements DrukiHTMLParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse($content) {
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

        continue;
      }

      // If no other is detected, treat is as content.
      $structure_copy = $structure;
      $previous_element = end($structure_copy);
      $key = key($structure_copy);

      if ($previous_element && $structure[$key]['type'] == 'content') {
        // If previous element was content too, we append current to it.
        $structure[$key]['content'] .= $dom_element->ownerDocument->saveHTML($dom_element);
      }
      else {
        $structure[] = [
          'type' => 'content',
          'content' => $dom_element->ownerDocument->saveHTML($dom_element),
        ];
      }
    }

    return $structure;
  }

  /**
   * Checks is node element is heading.
   *
   * @param string $node_name
   *   The DOM Element node name.
   *
   * @return bool
   *   TRUE if heading, FALSE if not.
   */
  protected function isHeading($node_name) {
    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    return in_array($node_name, $heading_elements);
  }

  /**
   * Checks is node element is code.
   *
   * @param string $node_name
   *   The DOM Element node name.
   *
   * @return bool
   *   TRUE if code, FALSE if not.
   */
  protected function isCode($node_name) {
    $content_elements = ['pre'];

    return in_array($node_name, $content_elements);
  }

  /**
   * Checks is node element is image.
   *
   * @param \DOMElement $node_name
   *   The DOM Element object.
   *
   * @return array
   *   An array containing src and alt attribute values, NULL if not image.
   */
  protected function isImage(\DOMElement $dom_element) {
    $crawler = new Crawler($dom_element);
    $image = $crawler->filter('img')->extract(['src', 'alt']);
    if (!empty($image)) {
      return $image;
    }
  }

}
