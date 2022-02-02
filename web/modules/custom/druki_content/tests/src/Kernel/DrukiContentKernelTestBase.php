<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\Kernel;

use Drupal\Tests\druki\Kernel\DrukiKernelTestBase;

/**
 * Provides a base test class for druki_content Kernel tests.
 */
abstract class DrukiContentKernelTestBase extends DrukiKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'druki_content',
  ];

}
