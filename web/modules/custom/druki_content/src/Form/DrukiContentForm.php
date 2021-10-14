<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the druki content entity edit forms.
 */
final class DrukiContentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    /** @var \Drupal\druki_content\Entity\DrukiContentInterface $druki_content */
    $druki_content = $this->entity;

    // Force advanced to be presented and change type to container.
    $form['advanced'] = [
      '#type' => 'container',
      '#weight' => 99,
      '#attributes' => [
        'class' => [
          'entity-meta',
        ],
      ],
    ];

    $form = parent::form($form, $form_state);

    $form['content_info'] = [
      '#type' => 'container',
      '#group' => 'advanced',
      '#weight' => -10,
      '#title' => $this->t('Git Information'),
      '#attributes' => ['class' => ['entity-meta__header']],
      '#tree' => TRUE,
    ];

    $form['content_info']['slug'] = [
      '#type' => 'item',
      '#title' => $this->t('Slug'),
      '#markup' => $druki_content->getSlug(),
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
    ];

    if (!$druki_content->get('core')->isEmpty()) {
      $form['content_info']['core'] = [
        '#type' => 'item',
        '#title' => $this->t('Core version'),
        '#markup' => $druki_content->get('core')->value,
        '#wrapper_attributes' => [
          'class' => [
            'container-inline',
          ],
        ],
      ];
    }

    $form['content_info']['relative_pathname'] = [
      '#type' => 'item',
      '#title' => $this->t('Relative pathname'),
      '#markup' => $druki_content->get('relative_pathname')->value,
      '#wrapper_attributes' => [
        'class' => [
          'container-inline',
        ],
      ],
    ];

    $form['#attached']['library'][] = 'druki_content/form';

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
    $logger_arguments = $message_arguments + ['link' => \render($link)];

    if ($result == \SAVED_NEW) {
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
