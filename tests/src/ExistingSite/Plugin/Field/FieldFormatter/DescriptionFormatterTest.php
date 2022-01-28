<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Plugin\Field\FieldFormatter;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Tests\druki\Traits\EntityCleanupTrait;
use Drupal\Tests\druki_author\Traits\AuthorCreationTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides a test for 'description' formatter for an author entity.
 *
 * @coversDefaultClass \Drupal\druki_author\Plugin\Field\FieldFormatter\DescriptionFormatter
 */
final class DescriptionFormatterTest extends ExistingSiteBase {

  use AuthorCreationTrait;
  use EntityCleanupTrait;
  use ProphecyTrait;

  /**
   * The current language ID.
   */
  protected string $currentLanguage = 'en';

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->cleanupEntities();
    parent::tearDown();
  }

  /**
   * Tests that formatter works as expected.
   */
  public function testFormatter(): void {
    $author = $this->createAuthor();
    $author->setDescription([
      'en' => 'Hello, world!',
      'ru' => 'Привет, мир!',
    ]);

    $display_options = [
      'type' => 'druki_author_description',
    ];

    $this->currentLanguage = 'en';
    $result = $author->get('description')->view($display_options);
    $this->assertEquals('Hello, world!', $result['0']['#markup']);

    $this->currentLanguage = 'ru';
    $result = $author->get('description')->view($display_options);
    $this->assertEquals('Привет, мир!', $result['0']['#markup']);

    $this->currentLanguage = 'de';
    $result = $author->get('description')->view($display_options);
    $this->assertArrayNotHasKey('0', $result);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storeEntityIds(['druki_author']);

    $self = $this;
    $language_manager = $this->prophesize(LanguageManagerInterface::class);
    $language_manager->getLanguages(Argument::any())->willReturn([]);
    $language_manager->getCurrentLanguage(Argument::any())->will(static function () use ($self) {
      $language = $self->prophesize(LanguageInterface::class);
      $language->getId()->willReturn($self->currentLanguage);
      return $language;
    });
    $this->container->set('language_manager', $language_manager->reveal());
  }

}
