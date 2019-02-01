<?php

namespace Drupal\markdown\Plugin\Markdown\Extension;

use Drupal\Core\Form\FormStateInterface;
use Drupal\markdown\Plugin\Filter\MarkdownFilterInterface;
use Drupal\markdown\Plugin\Markdown\MarkdownGuidelinesAlterInterface;
use Drupal\user\Entity\User;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\InlineParserContext;

/**
 * Class Important.
 *
 * @MarkdownExtension(
 *   id = "druki_parser_important",
 *   parser = "thephpleague/commonmark",
 *   label = @Translation("Parser for important paragraph"),
 *   description = @Translation("Parse content directed for important paragraph type."),
 *   filter = "_default"
 * )
 */
class Important extends CommonMarkExtension implements InlineParserInterface, MarkdownGuidelinesAlterInterface {

  /**
   * {@inheritdoc}
   */
  public function alterGuidelines(array &$guides = []) {

  }

  /**
   * {@inheritdoc}
   */
  public function getCharacters() {
    return ['!@#'];
  }

  /**
   * {@inheritdoc}
   */
  public function parse(InlineParserContext $inline_context) {
    dump('TEST');

    return TRUE;
  }

}
