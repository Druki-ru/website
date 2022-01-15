<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\Filter;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Druki\Tests\Traits\EntityCleanupTrait;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\druki_content\Plugin\Filter\InternalLinks;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for internal link filter plugin.
 *
 * @coversDefaultClass \Drupal\druki_content\Plugin\Filter\InternalLinks
 */
final class InternalLinksTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;
  use EntityCleanupTrait;
  use ProphecyTrait;

  /**
   * The internal links plugin.
   */
  protected InternalLinks $filterPlugin;

  /**
   * The cache backend.
   */
  protected CacheBackendInterface $cache;

  /**
   * Tests that simple text is not processed.
   *
   * The filter should trigger only on text with specific link with custom
   * attribute.
   */
  public function testSimpleText(): void {
    $text = 'Hello, World!';
    $filtered_text = $this->filterPlugin->process($text, 'ru');
    $this->assertSame($text, $filtered_text->getProcessedText());
  }

  /**
   * Tests that links replaced as expected.
   */
  public function testWithLinks(): void {
    $text = '<a href="100/index.md" data-druki-internal-link-filepath="public://druki-content-source/docs/ru/drupal/index.md">Drupal 100</a>';
    $filtered_text = $this->filterPlugin->process($text, 'ru');
    // The entity which link reffers to does not exists at this point. We expect
    // such link to be just hash-link.
    $this->assertSame('<a href="#">Drupal 100</a>', $filtered_text->getProcessedText());

    $content = $this->createDrukiContent(['type' => 'documentation']);
    $content->set('relative_pathname', 'docs/ru/drupal/100/index.md');
    $content->save();

    // Reset cache so plugin will try to find entity again.
    $this->cache->deleteAll();

    $filtered_text = $this->filterPlugin->process($text, 'ru');
    $expected = '<a href="' . $content->toUrl()->toString() . '">Drupal 100</a>';
    $this->assertSame($expected, $filtered_text->getProcessedText());
  }

  /**
   * Tests that tips are presented.
   */
  public function testTips(): void {
    $this->assertIsString($this->filterPlugin->tips());
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $source_content_settings = $this->container->get('druki_content.repository.content_source_settings');

    $file_system = $this->prophesize(FileSystemInterface::class);
    $file_system->realpath($source_content_settings->getRepositoryUri())->willReturn('/var/www/content');
    $file_system->realpath('public://druki-content-source/docs/ru/drupal/index.md')
      ->willReturn('/var/www/content/docs/ru/drupal/index.md');
    $this->container->set('file_system', $file_system->reveal());

    $this->filterPlugin = $this->container->get('plugin.manager.filter')
      ->createInstance('druki_content_internal_links');
    $this->cache = $this->container->get('cache.static');
    $this->storeEntityIds(['druki_content']);
  }

}
