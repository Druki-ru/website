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
            'php' => [
              'php.md' => 'This file is not be using as content.',
              'index.md' => 'Drupal PHP code standards.',
            ],
          ],
          'drupal' => [
            'index.md' => \file_get_contents(__DIR__ . '/../../fixtures/source-content.md'),
          ],
          'redirects.csv' => \file_get_contents(__DIR__ . '/../../fixtures/redirects.csv'),
        ],
        'en' => [
          'standards' => [
            'php' => [
              'index.md' => 'Drupal PHP code standards.',
            ],
          ],
          'drupal' => [
            'index.md' => 'Drupal description.',
          ],
        ],
        'de' => [],
      ],
      'README.md' => "Readme file.",
    ]);
  }

  /**
   * Prepares file structure which was updated.
   */
  protected function setupFakeSourceDirUpdate(): vfsStreamDirectory {
    return vfsStream::setup('content', NULL, [
      'docs' => [
        'ru' => [
          'drupal' => [
            'index.md' => \file_get_contents(__DIR__ . '/../../fixtures/source-content-2.md'),
          ],
          'redirects.csv' => \file_get_contents(__DIR__ . '/../../fixtures/redirects-2.csv'),
        ],
      ],
    ]);
  }

}
