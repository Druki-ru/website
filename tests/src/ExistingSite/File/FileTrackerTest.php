<?php

namespace Druki\Tests\ExistingSite\File;

use Drupal\Component\Utility\Crypt;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\media\Entity\Media;
use Drupal\media\Entity\MediaType;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for file tracking mechanism.
 *
 * @coversDefaultClass \Drupal\druki\File\FileTracker
 */
final class FileTrackerTest extends ExistingSiteBase {

  /**
   * The file tracker.
   *
   * @var \Drupal\druki\File\FileTracker
   */
  protected $fileTracker;

  /**
   * Test automatic tracking file.
   *
   * @covers ::track
   */
  public function testAutoTrack(): void {
    $file = $this->createTestFile();
    $file_hash = \md5_file($file->getFileUri());
    $this->assertSame($file_hash, $file->get('druki_file_hash')->value);
  }

  /**
   * Creates file entity for testing.
   *
   * @return \Drupal\file\FileInterface
   *   The file entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createTestFile(): FileInterface {
    $file = File::create([
      'uri' => 'public://' . Crypt::randomBytesBase64(7) . '.jpg',
    ]);
    \file_put_contents($file->getFileUri(), \file_get_contents('core/tests/fixtures/files/image-2.jpg'));
    // This will trigger automatic tracking.
    $file->setPermanent();
    $file->save();
    $this->markEntityForCleanup($file);
    return $file;
  }

  /**
   * Test for duplication detection.
   *
   * @covers ::checkDuplicate
   */
  public function testCheckDuplicate(): void {
    $file_1 = $this->createTestFile();
    $file_2 = $this->createTestFile();

    // Make sure they have different URIs, so this is a two different files.
    $this->assertNotEquals($file_1->getFileUri(), $file_2->getFileUri());
    $existed_duplicate = $this->fileTracker->checkDuplicate($file_2->getFileUri());
    $this->assertEquals($file_1->id(), $existed_duplicate->id());
  }

  /**
   * Test updater.
   *
   * @covers ::updateTrackingInformation
   */
  public function testUpdateTrackingInformation(): void {
    $file = $this->createTestFile();

    // Since there is no possible way for new files to exist without value, we
    // intentionally reset value via SQL. We can't use API since it will trigger
    // presave hooks and value will be set again.
    $connection = \Drupal::database();
    $connection->update('file_managed')
      ->fields([
        'druki_file_hash' => NULL,
      ])
      ->condition('fid', $file->id())
      ->execute();

    // Fetch actual entity.
    $file = File::load($file->id());
    // Make sure value is not set.
    $this->assertEquals(TRUE, $file->get('druki_file_hash')->isEmpty());

    $this->fileTracker->track($file);
    $file->save();

    // Fetch actual entity.
    $file = File::load($file->id());
    $this->assertEquals(FALSE, $file->get('druki_file_hash')->isEmpty());
  }

  /**
   * Test ability to find media entity that use provided media file.
   */
  public function testFindingMedia(): void {
    $file = $this->createTestFile();
    $image_media_type = MediaType::load('image');

    $media = Media::create([
      'bundle' => $image_media_type->id(),
    ]);
    $source_field = $image_media_type->getSource()->getSourceFieldDefinition($image_media_type)->getName();
    $media->set($source_field, ['target_id' => $file->id()]);
    $media->save();
    $this->markEntityForCleanup($media);

    $result = $this->fileTracker->getMediaForFile($file);
    $this->assertEquals($media->id(), $result->id());
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->fileTracker = $this->container->get('druki.file_tracker');
  }

}
