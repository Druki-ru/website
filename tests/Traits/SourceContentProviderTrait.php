<?php

namespace Druki\Tests\Traits;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Provides trait to generate fake source content files and structure.
 */
trait SourceContentProviderTrait {

  /**
   * Prepare fake structure for source content with some contents.
   */
  protected function setupFakeSourceDir(): vfsStreamDirectory {
    return vfsStream::setup('content', NULL, [
      'docs' => [
        'ru' => [
          'standards' => [
            'php.md' => 'Drupal PHP code standards.',
          ],
          'drupal.md' => 'Drupal description.',
        ],
        'en' => [
          'standards' => [
            'php.md' => 'Drupal PHP code standards.',
          ],
          'drupal.md' => 'Drupal description.',
        ],
        'de' => [],
      ],
      'README.md' => "Readme file.",
    ]);
  }

}
