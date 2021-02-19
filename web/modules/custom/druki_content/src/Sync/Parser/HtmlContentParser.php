<?php

namespace Drupal\druki_content\Sync\Parser;

use Drupal\druki_content\Sync\ParsedContent\Content\ContentList;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphCode;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphHeading;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphImage;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphNote;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphText;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatter;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface;
use Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterValue;
use Drupal\druki_content\Sync\ParsedContent\ParsedContent;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Parse HTML markup to structured value objects.
 */
final class HtmlContentParser {

  /**
   * Parses HTML to structured data.
   *
   * @param string $html
   *   The HTML with content.
   * @param string|null $filepath
   *   The filepath of parsed file. Will be used for internal links processing.
   *
   * @return \Drupal\druki_content\Sync\ParsedContent\ParsedContent
   *   The structured value object with content.
   */
  public function parse(string $html, ?string $filepath = NULL): ParsedContent {
    $crawler = new Crawler($html);
    // Move to body. We expect content here.
    $crawler = $crawler->filter('body');
    // For now we have this structure types:
    // - heading: Heading elements.
    // - content: Almost every content, p, ul, li, span and so on.
    // - image: Image tag.
    // - code: for pre > code.
    // Each content node after another merge to previous.
    $content = new ContentList();
    $meta_information = new FrontMatter();

    // Move through elements and structure them.
    foreach ($crawler->children() as $dom_element) {
      // Parse it once, or until it get valid. But actually only once.
      if (!$meta_information->valid()) {
        if ($this->parseFrontMatter($dom_element, $meta_information)) {
          continue;
        }
      }

      // Process internal links in priority mode.
      if ($filepath) {
        $this->processInternalLink($dom_element, $filepath);
      }

      if ($this->parseNote($dom_element, $content)) {
        continue;
      }

      if ($this->parseHeading($dom_element, $content)) {
        continue;
      }

      if ($this->parseCode($dom_element, $content)) {
        continue;
      }

      if ($this->parseImage($dom_element, $content)) {
        continue;
      }

      // If no other is detected, treat is as text.
      // If last element is also text, we append content to it.
      if ($content->end() instanceof ParagraphText) {
        $previous_text = $content->pop();
        $previous_text_content = $previous_text->getContent();
        $new_content = $previous_text_content . \PHP_EOL . $dom_element->ownerDocument->saveHTML($dom_element);
        $replace = new ParagraphText($new_content);
        $content->add($replace);
      }
      else {
        $text = new ParagraphText($dom_element->ownerDocument->saveHTML($dom_element));
        $content->add($text);
      }
    }

    return new ParsedContent($meta_information, $content);
  }

  /**
   * Parses meta information.
   *
   * Meta information is custom Markdown syntax and structure.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_content\Sync\ParsedContent\FrontMatter\FrontMatterInterface $meta_information
   *   The content meta information.
   *
   * @return bool|null
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseFrontMatter(\DOMElement $dom_element, FrontMatterInterface $meta_information): bool {
    $crawler = new Crawler($dom_element->ownerDocument->saveHTML($dom_element));
    $meta_block = $crawler->filter('div[data-druki-element="front-matter"]');

    if (\count($meta_block)) {
      $meta_array = \json_decode($meta_block->text(), TRUE);
      foreach ($meta_array as $key => $value) {
        $meta_value = new FrontMatterValue($key, $value);
        $meta_information->add($meta_value);
      }

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses internal links to another markdown files.
   *
   * @param \DOMNode $dom_element
   *   The DOM element to process.
   * @param string $filepath
   *   The filepath of file in which this link was found.
   */
  protected function processInternalLink(\DOMNode $dom_element, string $filepath): void {
    if (empty($dom_element->childNodes)) {
      return;
    }

    /** @var \DOMElement $child_node */
    foreach ($dom_element->childNodes as $child_node) {

      if ($child_node->nodeName == 'a') {
        $href = $child_node->getAttribute('href');

        // Must end up with Markdown extension: .md, .MD.
        if (!\preg_match("/.*\.md$/mi", $href)) {
          continue;
        }

        $child_node->setAttribute('data-druki-internal-link-filepath', $filepath);
      }

      $this->processInternalLink($child_node, $filepath);
    }
  }

  /**
   * Parses note.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_content\Sync\ParsedContent\Content\ContentList $content
   *   The value object of content list.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseNote(\DOMElement $dom_element, ContentList $content): ?bool {
    $crawler = new Crawler($dom_element->ownerDocument->saveHTML($dom_element));
    $note_element = $crawler->filter('div[data-druki-note]');

    if (\count($note_element)) {
      $element = $note_element->getNode(0);

      $value = '';

      foreach ($element->childNodes as $child) {
        $value .= $element->ownerDocument->saveHTML($child);
      }

      $note = new ParagraphNote($element->getAttribute('data-druki-note'), $value);
      $content->add($note);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses heading.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_content\Sync\ParsedContent\Content\ContentList $content
   *   The value object of content list.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseHeading(\DOMElement $dom_element, ContentList $content): bool {
    $node_name = $dom_element->nodeName;
    $heading_elements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    if (\in_array($node_name, $heading_elements)) {
      $heading = new ParagraphHeading($dom_element->nodeName, $dom_element->textContent);
      $content->add($heading);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses code.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_content\Sync\ParsedContent\Content\ContentList $content
   *   The value object of content list.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseCode(\DOMElement $dom_element, ContentList $content): bool {
    $node_name = $dom_element->nodeName;
    $code_elements = ['pre'];

    if (\in_array($node_name, $code_elements)) {
      $code = new ParagraphCode($dom_element->ownerDocument->saveHTML($dom_element));
      $content->add($code);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Parses image.
   *
   * @param \DOMElement $dom_element
   *   The DOM element to process.
   * @param \Drupal\druki_content\Sync\ParsedContent\Content\ContentList $content
   *   The value object of content list.
   *
   * @return bool
   *   TRUE if parsed successfully, NULL otherwise.
   */
  protected function parseImage(\DOMElement $dom_element, ContentList $content): bool {
    $crawler = new Crawler($dom_element);
    $image = $crawler->filter('img')->first();

    if (\count($image)) {
      $image_element = new ParagraphImage($image->attr('src'), $image->attr('alt'));
      $content->add($image_element);

      return TRUE;
    }

    return FALSE;
  }

}
