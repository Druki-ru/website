<?php

namespace Drupal\druki_parser\Service;

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
      if ($this->parseMetaInformation($dom_element, $structure)) {
        continue;
      }

      if ($this->parseHeading($dom_element, $structure)) {
        continue;
      }

      if ($this->parseCode($dom_element, $structure)) {
        continue;
      }

      if ($this->parseImage($dom_element, $structure)) {
        continue;
      }

      // If no other is detected, treat is as content.
      $structure_copy = $structure;
      $previous_element = end($structure_copy['content']);
      $key = key($structure_copy['content']);

      if ($previous_element && $structure['content'][$key]['type'] == 'content') {
        // If previous element was content too, we append current to it.
        $structure['content'][$key]['markup'] .= $dom_element->ownerDocument->saveHTML($dom_element);
      }
      else {
        $structure['content'][] = [
          'type' => 'content',
          'markup' => $dom_element->ownerDocument->saveHTML($dom_element),
        ];
      }
    }

    return $structure;
  }

  /**
   * Parses meta information.
   *
   * Meta information is custom Markdown syntax and structure.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param array $structure
   *   An array with existing structure.
   *
   * @return bool|null
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseMetaInformation(\DOMElement $dom_element, array &$structure) {
    // If meta information already in structure, this is not good. There must
    // be only one meta block. But if it happens, we just skip it and it becomes
    // content value.
    if (isset($structure['meta']) && !empty($structure['meta'])) {
      return NULL;
    }

    $crawler = new Crawler($dom_element->ownerDocument->saveHTML($dom_element));
    $meta_block = $crawler->filter('div[data-druki-meta=""]');

    if (count($meta_block)) {
      $meta_information = [];

      foreach ($crawler->filter('[data-druki-key][data-druki-value]') as $element) {
        $key = $element->getAttribute('data-druki-key');
        $value = $element->getAttribute('data-druki-value');
        $meta_information[$key] = $value;
      }

      $structure['meta'] = $meta_information;

      return TRUE;
    }
  }

  /**
   * Parses heading.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param array $structure
   *   An array with existing structure.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseHeading(\DOMElement $dom_element, array &$structure) {
    $node_name = $dom_element->nodeName;
    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    if (in_array($node_name, $heading_elements)) {
      $structure['content'][] = [
        'type' => 'heading',
        'level' => $dom_element->nodeName,
        'value' => $dom_element->textContent,
      ];

      return TRUE;
    }
  }

  /**
   * Parses code.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param array $structure
   *   An array with existing structure.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseCode(\DOMElement $dom_element, array &$structure) {
    $node_name = $dom_element->nodeName;
    $code_elements = ['pre'];

    if (in_array($node_name, $code_elements)) {
      $structure['content'][] = [
        'type' => 'code',
        'value' => $dom_element->ownerDocument->saveHTML($dom_element),
      ];

      return TRUE;
    }
  }

  /**
   * Parses image.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param array $structure
   *   An array with existing structure.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseImage(\DOMElement $dom_element, array &$structure) {
    $crawler = new Crawler($dom_element);
    $image = $crawler->filter('img')->first();
    if (count($image)) {
      $structure['content'][] = [
        'type' => 'image',
        'src' => $image->attr('src'),
        'alt' => $image->attr('alt'),
      ];

      return TRUE;
    }
  }

}
