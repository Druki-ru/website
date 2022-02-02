<?php

declare(strict_types=1);

namespace Drupal\druki\Markdown\CommonMark\Block\Parser;

use Drupal\druki\Markdown\CommonMark\Block\Element\AsideElement;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * Provides <Aside> element parser.
 */
final class AsideParser implements BlockParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(ContextInterface $context, Cursor $cursor): bool {
    if ($cursor->isIndented()) {
      return FALSE;
    }

    if ($cursor->getSubstring(0, 6) !== '<Aside') {
      return FALSE;
    }

    // This is block parser, not inline. Do not allow use of '<Aside></Aside>'.
    // Open tag should be single on that line.
    $match = $cursor->match('/<Aside[^><]*>\s*$/');
    if (!$match) {
      return FALSE;
    }

    // Default and fallback value for aside type.
    $aside_type = 'note';
    \preg_match('/\s+type="(note|important|warning|deprecated)"/', $match, $type);
    if (!empty($type)) {
      $aside_type = $type[1];
    }

    $aside_header = NULL;
    \preg_match('/\s+header="(.+)"/', $match, $header);
    if (!empty($header)) {
      $aside_header = $header[1];
    }

    $aside_element = new AsideElement($aside_type, $aside_header);
    $context->addBlock($aside_element);

    return TRUE;
  }

}
