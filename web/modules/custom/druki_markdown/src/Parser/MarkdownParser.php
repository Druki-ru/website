<?php

namespace Drupal\druki_markdown\Parser;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\markdown\Markdown;

/**
 * Parser for Markdown.
 *
 * @package Drupal\druki_parser\Service
 */
class MarkdownParser implements MarkdownParserInterface {

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
    // to it, markdown will be converted without extensions. So we force in
    // code to handle it via admin user.
    $this->markdownParser = $markdown->getParser('thephpleague/commonmark', 'markdown', $user);
  }

  /**
   * {@inheritdoc}
   */
  public function parse(string $content): string {
    return $this->markdownParser->convertToHtml($content);
  }

}
