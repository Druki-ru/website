<?php

namespace Drupal\druki_content\EventSubscriber;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\druki_content\Common\ContentQueueItem;
use Drupal\druki_git\Event\DrukiGitEvent;
use Drupal\druki_git\Event\DrukiGitEvents;
use Drupal\druki_parser\Service\DrukiFolderParserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DrukiContentSubscriber
 *
 * @package Drupal\druki_content\EventSubscriber
 */
class DrukiContentSubscriber implements EventSubscriberInterface {

  /**
   * The queue object.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * The folder parser.
   *
   * @var \Drupal\druki_parser\Service\DrukiFolderParserInterface
   */
  protected $folderParser;

  /**
   * The druki content storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $drukiContentStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * DrukiContentSubscriber constructor.
   *
   * @param \Drupal\Core\Queue\QueueFactory $queue
   *   The queue factory.
   * @param \Drupal\druki_parser\Service\DrukiFolderParserInterface $folder_parser
   *   The folder parser.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function __construct(
    QueueFactory $queue,
    DrukiFolderParserInterface $folder_parser,
    Connection $database,
    EntityTypeManagerInterface $entity_type_manager
  ) {

    $this->queue = $queue->get('druki_content_updater');
    $this->folderParser = $folder_parser;
    $this->database = $database;
    $this->drukiContentStorage = $entity_type_manager->getStorage('druki_content');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      DrukiGitEvents::FINISH_PULL => ['onPullFinish'],
    ];
  }

  /**
   * Reacts on successful pull
   *
   * @param \Drupal\druki_git\Event\DrukiGitEvent $event
   *   The git event.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function onPullFinish(DrukiGitEvent $event): void {
    $files = $this->folderParser->parse($event->git()->getRepositoryPath());
    // Delete queue and all items in it if this event fired.
    // This will clean Queue from items which did not make it since last queue
    // was generated. Also this is protect queue from processing same content
    // multiple times. This is effective for multiple pulls in the row, such as
    // hotfix followup commit, which actually just multiply work for nothing.
    $this->queue->deleteQueue();

    // The relative pathname is the main identifier for docs now.
    // We loads all of them existed on site.
    $available_content = $this->currentContentPathnames();

    /** @var \Symfony\Component\Finder\SplFileInfo[] $items */
    foreach ($files as $langcode => $items) {
      foreach ($items as $item) {
        // If content found in existed, remove it from that list.
        unset($available_content[$item->getRelativePathname()]);

        $last_commit_id = $event
          ->git()
          ->getFileLastCommitId($item->getRelativePathname());

        $contribution_statistics = $event
          ->git()
          ->getFileCommitsInfo($item->getRelativePathname());

        $queue_item = new ContentQueueItem(
          $langcode,
          $item->getPathname(),
          $item->getRelativePathname(),
          $item->getFilename(),
          $last_commit_id,
          $contribution_statistics
        );

        $this->queue->createItem($queue_item);
      }
    }

    // The content that remains in available content and not excluded during
    // parsing - content which is removed from repository or moved to another
    // place. This content must be removed before update process.
    if (!empty($available_content)) {
      $content_to_remove = $this->drukiContentStorage->loadMultiple($available_content);
      $this->drukiContentStorage->delete($content_to_remove);
    }
  }

  /**
   * Loads the list of exists relative pathnames.
   *
   * Loads all "relative_pathname" values from existing content.
   *
   * @code
   * [
   *   "docs/ru/code-of-conduct.md" => 1,
   *   "docs/ru/drupal.md" => 2,
   * ]
   * @endcode
   *
   * @return array
   *   The array with relative pathnames.
   */
  protected function currentContentPathnames(): array {
    return $this
      ->database
      ->select('druki_content_field_data', 'fd')
      ->fields('fd', ['internal_id', 'relative_pathname'])
      ->execute()
      ->fetchAllKeyed(1, 0);
  }

}
