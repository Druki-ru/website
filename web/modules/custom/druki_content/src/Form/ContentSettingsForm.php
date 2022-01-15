<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Configuration form for a druki content entity type.
 *
 * @todo Consider remove it and all th rest UI responsible for configuring an
 *   entity. Or think about using it for set up Git information for content
 *   source.
 */
final class ContentSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'druki_content_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['info']['#markup'] = new TranslatableMarkup('There are no any settings for entity at this time.');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // This form doesn't support any submissions.
  }

}
