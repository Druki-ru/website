<?php

namespace Drupal\druki_content\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;

/**
 * Provides an extra field to display 'Edit this page' button.
 *
 * @ExtraFieldDisplay(
 *   id = "edit_remote",
 *   label = @Translation("Edit this page (remote)"),
 *   visible = TRUE,
 *   bundles = {
 *     "druki_content.documentation",
 *   },
 * )
 */
final class EditRemote extends ExtraFieldDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    $text = new TranslatableMarkup('Edit this page');
    $options = [
      'attributes' => [
        'class' => ['content-edit-remote'],
        'rel' => 'noopener nofollow',
      ],
    ];
    return $entity->toLink($text, 'edit-remote', $options)->toRenderable();
  }

}
