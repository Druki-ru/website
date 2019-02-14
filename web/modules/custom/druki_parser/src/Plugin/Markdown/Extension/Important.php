<?php

namespace Drupal\druki_parser\Plugin\Markdown\Extension;

use Drupal\markdown\Plugin\Markdown\Extension\CommonMarkExtension;
use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;

/**
 * Class Important.
 *
 * @deprecated Move to CommonMark extensions.
 *
 * @MarkdownExtension(
 *   id = "druki_parser_important",
 *   parser = "thephpleague/commonmark",
 *   label = @Translation("Parser for important paragraph"),
 *   description = @Translation("Parse content directed for important paragraph
 *   type.")
 * )
 */
class Important extends CommonMarkExtension implements BlockParserInterface {

  public function defaultSettings() {
    return [
      // @todo finish when major work will be done. This is not so important for
      // starting.
      'enabled' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCharacters() {
    return ['!'];
  }

  /**
   * Get the name of the parser
   *
   * Note that this must be unique with its block type.
   *
   * @return string
   */
  public function getName() {
    return 'druki_parser_important';
  }

  /**
   * @param ContextInterface $context
   * @param Cursor $cursor
   *
   * @return bool
   */
  public function parse(ContextInterface $context, Cursor $cursor) {
    if ($cursor->match("/!!!(warning|info)/")) {
      $previous_state = $cursor->saveState();

      return TRUE;
    }

    return FALSE;
  }

}
