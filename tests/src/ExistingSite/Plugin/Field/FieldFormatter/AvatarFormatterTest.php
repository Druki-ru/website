<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Crypt;
use Drupal\file\Entity\File;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use Drupal\Tests\druki_author\Traits\AuthorCreationTrait;
use weitzman\DrupalTestTraits\Entity\MediaCreationTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides a test for avatar formatter.
 *
 * @coversDefaultClass \Drupal\druki_author\Plugin\Field\FieldFormatter\AvatarFormatter
 */
final class AvatarFormatterTest extends ExistingSiteBase {

  use AuthorCreationTrait;
  use MediaCreationTrait;
  use EntityCleanupTrait;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storeEntityIds(['media', 'file', 'druki_author']);
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Test that formatter works as expected.
   */
  public function testFormatter(): void {
    $author = $this->createAuthor();

    $display_options = [
      'type' => 'druki_author_avatar',
      'settings' => [
        'image_style' => '60_60',
      ],
    ];

    $result = $author->get('image')->view($display_options);
    // An empty field without an image should display placeholder.
    $this->assertEquals('druki_avatar_placeholder', $result[0]['#type']);

    /** @var \Drupal\media\MediaInterface $media */
    $media = $this->createMedia([
      'bundle' => 'image',
    ]);
    $author->setImageMedia($media);
    $result = $author->get('image')->view($display_options);
    // We still expect placeholder, because the media itself doesn't contain
    // an image, we didn't set a file, so it should fallback to placeholder.
    $this->assertEquals('druki_avatar_placeholder', $result[0]['#type']);

    $file = File::create([
      'uri' => 'public://' . Crypt::randomBytesBase64(7) . '.jpg',
    ]);
    \file_put_contents($file->getFileUri(), \file_get_contents('core/tests/fixtures/files/image-2.jpg'));
    $file->save();
    $source_field = $media->getSource()->getConfiguration()['source_field'];
    $media->set($source_field, $file);

    $result = $author->get('image')->view($display_options);
    $this->assertEquals('image_style', $result[0]['#theme']);
  }

}
