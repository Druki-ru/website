<?php

namespace Drupal\druki_markdown\CommonMark\Block\Parser;

use Drupal\druki_markdown\CommonMark\Block\Element\FrontMatterElement;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * Provides Front Matter parser.
 */
class FrontMatterParser implements BlockParserInterface {

  /**
   * {@inheritdoc}
   */
  public function parse(ContextInterface $context, Cursor $cursor): bool {
    // Only works for metadata found at the beginning of the file. Other "---"
    // will be replaced as expected with "<hr />".
    if ($context->getLineNumber() > 1) {
      return FALSE;
    }

    if ($cursor->isIndented()) {
      return FALSE;
    }

    $meta_information = $cursor->match("/^\-{3}$/");
    if (!$meta_information) {
      return FALSE;
    }

    $context->addBlock(new FrontMatterElement());

    return TRUE;
  }

}
