<?php

namespace Druki\Tests\ExistingSite\Sync\ParsedContent\Content;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphImage;
use org\bovigo\vfs\vfsStream;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for text loader into paragraph.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphImageLoader
 */
final class ParagraphImageLoaderTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;

  protected function setUp(): void {
    $this->markTestSkipped('This test must be done after ParagraphImageLoader refactoring is ended.');
  }

  /**
   * Testing processing image from localhost.
   *
   * @covers ::process
   */
  public function testLocalFile(): void {
    $git_settings_config = $this->prophesize(ImmutableConfig::class);
    $git_settings_config->get('repository_path')->willReturn('123');

    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $config_factory->get('druki_git.git_settings')
      ->willReturn($git_settings_config->reveal());

    $this->container->set('config.factory', $config_factory->reveal());


    $file_system = vfsStream::setup();


    $paragraph_image = new ParagraphImage('cat.jpg', 'This is the cat!');


    $druki_content = $this->createDrukiContent();
    $content_loader = $this->container->get('druki_content.parsed_content_loader');
    $content_loader->process($paragraph_image, $druki_content);

    $this->assertTrue(TRUE);
  }

}
