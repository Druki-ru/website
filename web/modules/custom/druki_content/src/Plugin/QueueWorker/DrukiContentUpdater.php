<?php

namespace Drupal\druki_content\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * @QueueWorker(
 *   id = "druki_content_updater",
 *   title = @Translation("Druki content updater."),
 *   cron = {"time" = 60}
 * )
 */
class DrukiContentUpdater extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   *
   * @see DrukiContentSubscriber::onPullFinish().
   */
  public function processItem($data) {

  }

}
