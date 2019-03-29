<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the druki content entity edit forms.
 */
class DrukiContentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $form['git_information'] = [
      '#markup' => '<div>@todo git info</div>',
      '#group' => 'advanced',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): void {

    $entity = $this->getEntity();
    $result = $entity->save();
    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()
        ->addStatus($this->t('New druki content %label has been created.', $message_arguments));
      $this->logger('druki_content')
        ->notice('Created new druki content %label', $logger_arguments);
    }
    else {
      $this->messenger()
        ->addStatus($this->t('The druki content %label has been updated.', $message_arguments));
      $this->logger('druki_content')
        ->notice('Created new druki content %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.druki_content.canonical', ['druki_content' => $entity->id()]);
  }

}
