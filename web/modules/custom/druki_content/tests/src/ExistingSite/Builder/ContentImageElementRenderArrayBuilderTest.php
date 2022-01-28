<?php

declare(strict_types=1);

namespace Drupal\Tests\druki_content\ExistingSite\Builder;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\druki\Repository\MediaImageRepositoryInterface;
use Drupal\druki_content\Builder\ContentElementRenderArrayBuilderInterface;
use Drupal\druki_content\Data\ContentElementBase;
use Drupal\druki_content\Data\ContentImageElement;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use GuzzleHttp\Client;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for content image element render array builder.
 *
 * @coversDefaultClass \Drupal\druki_content\Builder\ContentImageElementRenderArrayBuilder
 */
final class ContentImageElementRenderArrayBuilderTest extends ExistingSiteBase {

  use ProphecyTrait;
  use EntityCleanupTrait;

  /**
   * The builder.
   */
  protected ContentElementRenderArrayBuilderInterface $builder;

  /**
   * The image repository.
   */
  protected MediaImageRepositoryInterface $repository;

  /**
   * Tests that applicable test works as expected.
   *
   * @covers ::isApplicable()
   */
  public function testIsApplicable(): void {
    $broken_element = new class() extends ContentElementBase {

    };
    $this->assertFalse($this->builder->isApplicable($broken_element));

    $valid_element = new ContentImageElement('https://example.com/img.jpg', 'Hello, World!');
    $this->assertTrue($this->builder->isApplicable($valid_element));
  }

  /**
   * Tests that build works as expected.
   *
   * @covers ::build()
   */
  public function testBuild(): void {
    $element = new ContentImageElement('https://example.com/img.jpg', 'Hello, World!');
    /** @var \Drupal\media\MediaInterface $media */
    $media = $this->repository->saveByUri($element->getSrc(), $element->getAlt());
    $source_field = $media->getSource()->getConfiguration()['source_field'];
    /** @var \Drupal\file\FileInterface $file */
    $file = $media->get($source_field)->first()->get('entity')->getValue();
    $expected = [
      '#theme' => 'druki_photoswipe_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $element->getAlt(),
      '#responsive_image_style_id' => 'paragraph_druki_image_thumbnail',
      '#photoswipe_image_style_id' => 'paragraph_druki_image_big_image',
    ];
    $cache = new CacheableMetadata();
    $cache->addCacheableDependency($media);
    $cache->addCacheableDependency($file);
    $cache->applyTo($expected);
    $this->assertEquals($expected, $this->builder->build($element));
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
    $image = \file_get_contents('core/tests/fixtures/files/image-2.jpg');

    $image_response_mock = $this->prophesize(ResponseInterface::class);
    $image_response_mock->getBody()->willReturn($image);

    $http_client_mock = $this->prophesize(Client::class);
    $http_client_mock->get('https://example.com/img.jpg')
      ->willReturn($image_response_mock->reveal());
    $this->container->set('http_client', $http_client_mock->reveal());

    $this->builder = $this->container->get('druki_content.builder.content_image_element_render_array');
    $this->repository = $this->container->get('druki.repository.media_image');

    // Image will be fetched and creates media and file entity.
    $this->storeEntityIds(['media', 'file']);
  }

}
