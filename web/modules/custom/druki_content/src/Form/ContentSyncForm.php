<?php

namespace Drupal\druki_content\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\druki\Queue\EntitySyncQueueManagerInterface;
use Drupal\druki_content\Builder\ContentSyncQueueBuilderInterface;
use Drupal\druki_content\Event\RequestSourceContentSyncEvent;
use Drupal\druki_content\Event\RequestSourceContentUpdateEvent;
use Drupal\druki_content\Repository\ContentSourceSettingsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Configuration form for a druki content entity type.
 */
final class ContentSyncForm extends FormBase {

  /**
   * The queue manager.
   */
  protected EntitySyncQueueManagerInterface $queueManager;

  /**
   * The event dispatcher.
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * The content source settings.
   */
  protected ContentSourceSettingsInterface $contentSourceSettings;

  /**
   * The queue builder.
   */
  protected ContentSyncQueueBuilderInterface $queueBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new static();
    $instance->queueManager = $container->get('druki_content.queue.content_sync_manager');
    $instance->queueBuilder = $container->get('druki_content.builder.content_sync_queue');
    $instance->contentSourceSettings = $container->get('druki_content.repository.content_source_settings');
    $instance->eventDispatcher = $container->get('event_dispatcher');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'druki_content_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['#tree'] = TRUE;

    $form = $this->buildQueueBuilderForm($form, $form_state);
    $form = $this->buildQueueManagerForm($form, $form_state);

    return $form;
  }

  /**
   * Builds element to build queue.
   *
   * @param array $form
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The modified form.
   */
  protected function buildQueueBuilderForm(array $form, FormStateInterface $form_state): array {
    $form['queue_builder'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Queue builder'),
    ];

    $form['queue_builder']['git'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Build from Git'),
      '#description' => new TranslatableMarkup('The "git pull" command will be invoked, which will trigger event and build queue from it.'),
    ];

    $form['queue_builder']['git']['build'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => new TranslatableMarkup('Pull from Git'),
      '#submit' => [[$this, 'createQueueFromGit']],
    ];

    $form['queue_builder']['folder'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Build from folder'),
    ];

    $form['queue_builder']['folder']['uri'] = [
      '#type' => 'textfield',
      '#title' => new TranslatableMarkup('URI'),
      '#description' => new TranslatableMarkup('The URI with source content.'),
      '#required' => TRUE,
      '#default_value' => $this->contentSourceSettings->getRepositoryUri(),
    ];

    $form['queue_builder']['folder']['build'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => new TranslatableMarkup('Create'),
      '#submit' => [[$this, 'createQueueFromFolder']],
    ];

    $form['queue_builder']['event'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Dispatch event'),
      '#description' =>  new TranslatableMarkup('Dispatch source content synchronization event. All subscribers will be notified and fill their queues and process tasks.'),
    ];

    $form['queue_builder']['event']['dispatch'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => new TranslatableMarkup('Dispatch'),
      '#submit' => [[$this, 'dispatchSyncEvent']],
    ];

    return $form;
  }

  /**
   * Builds control elements for queue manager.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The modified form.
   */
  protected function buildQueueManagerForm(array $form, FormStateInterface $form_state): array {
    $form['queue_manager'] = [
      '#type' => 'fieldset',
      '#title' => new TranslatableMarkup('Synchronization queue'),
    ];

    $form['queue_manager']['total'] = [
      '#markup' => '<p>' . new TranslatableMarkup('Current queue items: @count', ['@count' => $this->queueManager->getQueue()->numberOfItems()]) . '</p>',
    ];

    $form['queue_manager']['actions'] = [
      '#type' => 'actions',
      '#access' => (bool) $this->queueManager->getQueue()->numberOfItems(),
    ];
    $form['queue_manager']['actions']['clear'] = [
      '#type' => 'submit',
      '#button_type' => 'danger',
      '#value' => new TranslatableMarkup('Clear queue'),
      '#submit' => [[$this, 'clearQueue']],
    ];

    return $form;
  }

  /**
   * Clear queue from all items.
   */
  public function clearQueue(): void {
    $this->queueManager->delete();
  }

  /**
   * Builds new queue via Git pull.
   */
  public function createQueueFromGit(): void {
    $this->eventDispatcher->dispatch(new RequestSourceContentUpdateEvent());
  }

  /**
   * Builds new queue via Git pull.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function createQueueFromFolder(array $form, FormStateInterface $form_state): void {
    $uri = $form_state->getValue(['queue_builder', 'folder', 'uri']);
    if (!\is_dir($uri)) {
      return;
    }

    $this->queueBuilder->buildFromPath($uri);
  }

  /**
   * Dispatch content sync event.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function dispatchSyncEvent(array $form, FormStateInterface $form_state): void {
    $content_source_uri = $this->contentSourceSettings->getRepositoryUri();
    $event = new RequestSourceContentSyncEvent($content_source_uri);
    $this->eventDispatcher->dispatch($event);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // This form doesn't use default submit handler.
  }

}
