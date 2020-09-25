<?php

namespace Drupal\druki\Cron;

/**
 * Provides interface for cron processing using classes.
 */
interface CronProcessorInterface {

  /**
   * Process single cron operation.
   */
  public function process(): void;

}
