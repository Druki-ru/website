<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\druki_content\Traits\DrukiContentCreationTrait;

/**
 * Provides an abstract kernel test for easier testing 'druki' module.
 */
abstract class DrukiKernelTestBase extends KernelTestBase {

  use DrukiContentCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'file',
    'update',
    'search',
    'media',
    'user',
    'image',
    'druki',
  ];

}
