<?php

declare(strict_types=1);

namespace Drupal\druki_content\Parser;

use Drupal\druki_content\Data\ContentNoteElement;
use Drupal\druki_content\Data\ContentParserContext;

/**
 * Provides note element parser.
 *
 * @see \Drupal\druki\Markdown\CommonMark\Block\Renderer\NoteRenderer
 */
final class ContentHtmlNoteElementParser implements ContentHtmlElementParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(\DOMElement $element, ContentParserContext $context, ContentHtmlParser $parser): bool {
    if (!$element->hasAttribute('data-druki-note')) {
      return FALSE;
    }

    $note_type = $element->getAttribute('data-druki-note');
    $note_element = new ContentNoteElement($note_type);
    foreach ($element as $child_element) {
      // @todo Think how to better parse children.
    }
    $context->getContent()->addElement($note_element);
    return TRUE;
  }

}
