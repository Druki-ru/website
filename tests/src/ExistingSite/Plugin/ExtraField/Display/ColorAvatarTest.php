<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\ExtraField\Display;

use Druki\Tests\Traits\AuthorCreationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayManagerInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for color avatar extra field.
 *
 * @coversDefaultClass \Drupal\druki_author\Plugin\ExtraField\Display\ColorAvatar
 */
final class ColorAvatarTest extends ExistingSiteBase {

  use AuthorCreationTrait;

  /**
   * The extra field display manager.
   */
  protected ExtraFieldDisplayManagerInterface $extraFieldManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->extraFieldManager = $this->container->get('plugin.manager.extra_field_display');
  }

  /**
   * Tests that field prepares proper result.
   */
  public function testView(): void {
    $author = $this->createAuthor([
      'name_given' => 'Dries',
      'name_family' => 'Buytaert',
    ]);

    /** @var \Drupal\extra_field\Plugin\ExtraFieldDisplayInterface $plugin */
    $plugin = $this->extraFieldManager->createInstance('color_avatar');
    $result = $plugin->view($author);

    $this->assertEquals('druki_avatar_placeholder', $result['#theme']);
    $this->assertEquals('DB', $result['#initials']);
    $this->assertStringContainsString('hsl', $result['#background_color']);
    $this->assertStringContainsString('hsl', $result['#initials_color']);
  }

}
