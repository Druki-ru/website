<?php

namespace Drupal\druki_content\ParsedContent\Content;

use Drupal\druki_content\Entity\DrukiContentInterface;

/**
 * Provides loader for paragraph entity 'druki_code'.
 */
final class ParagraphCodeLoader extends ParagraphLoaderBase {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = ParagraphCode::class;

  /**
   * {@inheritdoc}
   */
  public function process($data, DrukiContentInterface $content): void {
    $paragraph = $this->paragraphStorage->create(['type' => $data->getParagraphType()]);
    $paragraph->set('druki_textarea_formatted', [
      'value' => $data->getContent(),
      'format' => $this->getDefaultTextFilter(),
    ]);

    $this->saveAndAppend($paragraph, $content);
  }

}
