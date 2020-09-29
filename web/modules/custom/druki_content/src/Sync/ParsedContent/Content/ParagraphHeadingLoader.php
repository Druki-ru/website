<?php

namespace Drupal\druki_content\Sync\ParsedContent\Content;

use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides loader for paragraph entity 'druki_heading'.
 */
final class ParagraphHeadingLoader extends ParagraphLoaderBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = ParagraphHeading::class;

  /**
   * {@inheritdoc}
   */
  public function process($data, DrukiContentInterface $content): void {
    $paragraph = $this->paragraphStorage->create(['type' => $data->getParagraphType()]);
    $paragraph->set('druki_textfield_formatted', [
      'value' => $data->getContent(),
      'format' => $this->getDefaultTextFilter(),
    ]);
    $paragraph->set('druki_heading_level', $data->getLevel());

    $this->saveAndAppend($paragraph, $content);
  }

}
