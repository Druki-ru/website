<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_author\Kernel;

use Drupal\Tests\druki\Kernel\DrukiKernelTestBase;
use Drupal\Tests\druki_content\Traits\DrukiContentCreationTrait;

/**
 * Provides an abstract kernel test for easier testing 'druki_author' module.
 */
abstract class DrukiAuthorKernelTestBase extends DrukiKernelTestBase {

  use DrukiContentCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'druki_author',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('media');
    $this->installEntitySchema('druki_author');
  }

}
