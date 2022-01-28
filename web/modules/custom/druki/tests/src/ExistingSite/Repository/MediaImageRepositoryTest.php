<?php

declare(strict_types=1);

namespace Drupal\Tests\druki\ExistingSite\Repository;

use Drupal\druki\Repository\MediaImageRepositoryInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for media image repository.
 *
 * @coversDefaultClass \Drupal\druki\Repository\MediaImageRepository
 */
final class MediaImageRepositoryTest extends ExistingSiteBase {

  use EntityCleanupTrait;
  use ProphecyTrait;

  /**
   * The content media image repository.
   */
  protected MediaImageRepositoryInterface $repository;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $image = \file_get_contents(\DRUPAL_ROOT . '/core/tests/fixtures/files/image-2.jpg');

    $image_response_mock = $this->prophesize(ResponseInterface::class);
    $image_response_mock->getBody()->willReturn($image);

    $http_client_mock = $this->prophesize(Client::class);
    $http_client_mock->get('https://example.com/img.jpg')
      ->willReturn($image_response_mock->reveal());
    $http_client_mock->get('https://example.com/404.jpg')
      ->willThrow(new TransferException());
    $this->container->set('http_client', $http_client_mock->reveal());

    $this->repository = $this->container->get('druki.repository.media_image');

    $this->storeEntityIds(['media', 'file']);
  }

  /**
   * Tests that loading by URI works as expected with external URLs.
   */
  public function testRepository(): void {
    $result = $this->repository->loadByUri('https://example.com/img.jpg');
    // Because there is no media file for that image.
    $this->assertNull($result);

    $result = $this->repository->loadByUri('https://example.com/404.jpg');
    // Any problem during loading file should return NULL.
    $this->assertNull($result);

    $result = $this->repository->saveByUri('https://example.com/img.jpg', 'Hello Kitty');
    $this->assertInstanceOf(MediaInterface::class, $result);
    $this->assertEquals($result->label(), 'Hello Kitty');
    $source_field = $result->getSource()->getConfiguration()['source_field'];
    $file = $result->get($source_field)
      ->first()
      ->get('entity')
      ->getValue();
    $this->assertInstanceOf(FileInterface::class, $file);
    $this->assertEquals(\md5_file(\DRUPAL_ROOT . '/core/tests/fixtures/files/image-2.jpg'), \md5_file($file->getFileUri()));

    // Now we have this file stored, it should find duplicate.
    $result = $this->repository->loadByUri('https://example.com/img.jpg');
    $this->assertInstanceOf(MediaInterface::class, $result);
    $file_2 = $result->get($source_field)
      ->first()
      ->get('entity')
      ->getValue();
    $this->assertSame($file->id(), $file_2->id());

    // Trying save it now should return existed media.
    $expected = $result;
    $result = $this->repository->saveByUri('https://example.com/img.jpg', 'Hello Kitty');
    $this->assertInstanceOf(MediaInterface::class, $result);
    $this->assertEquals($expected->id(), $result->id());
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

}
