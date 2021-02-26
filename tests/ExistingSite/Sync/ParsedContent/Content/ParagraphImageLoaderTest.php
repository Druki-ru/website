<?php

namespace Druki\Tests\ExistingSite\Sync\ParsedContent\Content;

use Druki\Tests\Traits\DrukiContentCreationTrait;
use Drupal\druki_content\Sync\ParsedContent\Content\ParagraphImage;
use Drupal\druki_git\Git\GitSettingsInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\paragraphs\ParagraphInterface;
use GuzzleHttp\Client;
use org\bovigo\vfs\vfsStream;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides tests for text loader into paragraph.
 *
 * @coversDefaultClass \Drupal\druki_content\Sync\ParsedContent\Content\ParagraphImageLoader
 */
final class ParagraphImageLoaderTest extends ExistingSiteBase {

  use DrukiContentCreationTrait;
  use ProphecyTrait;

  /**
   * The file used for testing.
   *
   * @var string
   */
  protected $testFilePath = __DIR__ . '/../../../../../web/core/tests/fixtures/files/image-1.png';

  /**
   * The content loader.
   *
   * @var \Drupal\druki_content\Sync\ParsedContent\ParsedContentLoader
   */
  protected $contentLoader;

  /**
   * The VFS root file system.
   *
   * @var \org\bovigo\vfs\vfsStreamDirectory
   */
  protected $vfsFileSystem;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->vfsFileSystem = vfsStream::setup();

    $git_settings = $this->prophesize(GitSettingsInterface::class);
    $git_settings->getRepositoryPath()->willReturn($this->vfsFileSystem->url());
    $this->container->set('druki_git.settings', $git_settings->reveal());

    $response = $this->prophesize(ResponseInterface::class);
    $response->getBody()->willReturn(\file_get_contents($this->testFilePath));
    $http_client = $this->prophesize(Client::class);
    $http_client->get('https://example.com/cat.png')->willReturn($response->reveal());
    $this->container->set('http_client', $http_client->reveal());

    // Load it after we replace git settings with the stub.
    $this->contentLoader = $this->container->get('druki_content.parsed_content_loader');
  }

  /**
   * Test behavior when file can be retrieved from remote or found locally.
   */
  public function testFileNotFound(): void {
    $paragraph_image = new ParagraphImage('image-1.jpg', 'This is the cat!');
    $druki_content = $this->createDrukiContent();
    $this->contentLoader->process($paragraph_image, $druki_content);
    $this->assertTrue($druki_content->get('content')->isEmpty());
  }

  /**
   * Testing processing image from localhost.
   */
  public function testLocalFile(): void {
    vfsStream::newFile('image-1.png')
      ->withContent(\file_get_contents($this->testFilePath))
      ->at($this->vfsFileSystem);

    $paragraph_image = new ParagraphImage('image-1.png', 'This is the cat!');
    $druki_content = $this->createDrukiContent();
    $this->contentLoader->process($paragraph_image, $druki_content);

    /** @var \Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem $first_paragraph */
    $first_paragraph_item = $druki_content->get('content')->first();
    $paragraph = $first_paragraph_item->entity;
    $this->assertTrue($paragraph instanceof ParagraphInterface);

    /** @var \Drupal\media\MediaInterface $media */
    $media = $paragraph->get('druki_image')->entity;
    $this->assertTrue($media instanceof MediaInterface);
    $this->markEntityForCleanup($media);

    $source_field = $media->getSource()->getConfiguration()['source_field'];
    /** @var \Drupal\file\FileInterface $file */
    $file = $media->get($source_field)->entity;
    $this->assertTrue($file instanceof FileInterface);
    $this->assertEquals(\md5_file($file->getFileUri()), $file->get('druki_file_hash')->getString());
    $this->markEntityForCleanup($file);
  }

  /**
   * Testing processing image from remote URL.
   */
  public function testRemoteFile(): void {
    $paragraph_image = new ParagraphImage('https://example.com/cat.png', 'This is the cat!');
    $druki_content = $this->createDrukiContent();
    $this->contentLoader->process($paragraph_image, $druki_content);

    /** @var \Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem $first_paragraph */
    $first_paragraph_item = $druki_content->get('content')->first();
    $paragraph = $first_paragraph_item->entity;
    $this->assertTrue($paragraph instanceof ParagraphInterface);

    /** @var \Drupal\media\MediaInterface $media */
    $media = $paragraph->get('druki_image')->entity;
    $this->assertTrue($media instanceof MediaInterface);
    $this->markEntityForCleanup($media);

    $source_field = $media->getSource()->getConfiguration()['source_field'];
    /** @var \Drupal\file\FileInterface $file */
    $file = $media->get($source_field)->entity;
    $this->assertTrue($file instanceof FileInterface);
    $this->assertEquals(\md5_file($file->getFileUri()), $file->get('druki_file_hash')->getString());
    $this->markEntityForCleanup($file);
  }

}
