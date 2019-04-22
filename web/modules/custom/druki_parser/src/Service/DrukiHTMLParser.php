<?php

namespace Drupal\druki_parser\Service;

use DOMElement;
use Drupal\druki_paragraphs\Content\ContentStructure;
use Drupal\druki_paragraphs\Content\MetaInformation;
use Drupal\druki_paragraphs\Content\MetaValue;
use Drupal\druki_paragraphs\Content\ParagraphCode;
use Drupal\druki_paragraphs\Content\ParagraphHeading;
use Drupal\druki_paragraphs\Content\ParagraphImage;
use Drupal\druki_paragraphs\Content\ParagraphNote;
use Drupal\druki_paragraphs\Content\ParagraphText;
use Drupal\search_api\Plugin\search_api\processor\Resources\Me;
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
  public function parse($content): ContentStructure {
    $crawler = new Crawler($content);
    // Move to body. We expect content here.
    $crawler = $crawler->filter('body');
    // For now we have this structure types:
    // - heading: Heading elements.
    // - content: Almost every content, p, ul, li, span and so on.
    // - image: Image tag.
    // - code: for pre > code.
    // Each content node after another merge to previous.
    $structure = new ContentStructure();

    // Move through elements and structure them.
    foreach ($crawler->children() as $dom_element) {
      if ($this->parseMetaInformation($dom_element, $structure)) {
        continue;
      }

      if ($this->parseNote($dom_element, $structure)) {
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

      // If no other is detected, treat is as text.
      // If last element is also text, we append content to it.
      if ($structure->lastContent() instanceof ParagraphText) {
        $new_text = $structure->lastContent()->getContent();
        $new_text .= $dom_element->ownerDocument->saveHTML($dom_element);
        $replace = new ParagraphText($new_text);
        $structure->replaceLastContent($replace);
      }
      else {
        $text = new ParagraphText($dom_element->ownerDocument->saveHTML($dom_element));
        $structure->addContent($text);
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
   * @param \Drupal\druki_paragraphs\Content\ContentStructure $structure
   *   The value object of content structure.
   *
   * @return bool|null
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseMetaInformation(DOMElement $dom_element, ContentStructure $structure): bool {
    // If meta information already in structure, this is not good. There must
    // be only one meta block. But if it happens, we just skip it and it becomes
    // content value.
    if ($structure->getMetaInformation() instanceof MetaInformation) {
      return FALSE;
    }

    $crawler = new Crawler($dom_element->ownerDocument->saveHTML($dom_element));
    $meta_block = $crawler->filter('div[data-druki-meta=""]');

    if (count($meta_block)) {
      $meta_information = new MetaInformation();

      foreach ($crawler->filter('[data-druki-key][data-druki-value]') as $element) {
        $key = $element->getAttribute('data-druki-key');
        $value = $element->getAttribute('data-druki-value');

        $meta_value = new MetaValue($key, $value);
        $meta_information->add($meta_value);
      }

      $structure->addMetaInformation($meta_information);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses note.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_paragraphs\Content\ContentStructure $structure
   *   The value object of content structure.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseNote(DOMElement $dom_element, ContentStructure $structure): ?bool {
    $crawler = new Crawler($dom_element->ownerDocument->saveHTML($dom_element));
    $note_element = $crawler->filter('div[data-druki-note]');

    if (count($note_element)) {
      $element = $note_element->getNode(0);

      $value = '';

      foreach ($element->childNodes as $child) {
        $value .= $element->ownerDocument->saveHTML($child);
      }

      $note = new ParagraphNote($element->getAttribute('data-druki-note'), $value);
      $structure->addContent($note);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses heading.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_paragraphs\Content\ContentStructure $structure
   *   The value object of content structure.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseHeading(DOMElement $dom_element, ContentStructure $structure): bool {
    $node_name = $dom_element->nodeName;
    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    if (in_array($node_name, $heading_elements)) {
      $heading = new ParagraphHeading($dom_element->nodeName, $dom_element->textContent);
      $structure->addContent($heading);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses code.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_paragraphs\Content\ContentStructure $structure
   *   The value object of content structure.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseCode(DOMElement $dom_element, ContentStructure $structure): bool {
    $node_name = $dom_element->nodeName;
    $code_elements = ['pre'];

    if (in_array($node_name, $code_elements)) {
      $code = new ParagraphCode($dom_element->ownerDocument->saveHTML($dom_element));
      $structure->addContent($code);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses image.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_paragraphs\Content\ContentStructure $structure
   *   The value object of content structure.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseImage(DOMElement $dom_element, ContentStructure $structure): bool {
    $crawler = new Crawler($dom_element);
    $image = $crawler->filter('img')->first();
    if (count($image)) {
      $image_element = new ParagraphImage($image->attr('src'), $image->attr('alt'));
      $structure->addContent($image_element);

      return TRUE;
    }

    return FALSE;
  }

}
