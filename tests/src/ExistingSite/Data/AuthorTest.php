<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Data;

use Druki\Tests\Traits\SourceContentProviderTrait;
use Drupal\druki_author\Data\Author;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for Author value object.
 *
 * This test is not Unit because the Author object reilies on LanguageManager
 * and CountryManager which require Drupal container to be initialized.
 *
 * @coversDefaultClass \Drupal\druki_author\Data\Author
 */
final class AuthorTest extends ExistingSiteBase {

  use SourceContentProviderTrait;

  /**
   * Tests that objects works as expected.
   */
  public function testObject(): void {
    $author = Author::createFromArray($this->getSampleId(), $this->getSampleValues());
    $this->assertEquals($this->getSampleId(), $author->getId());
    $this->assertEquals($this->getSampleValues()['name']['given'], $author->getNameGiven());
    $this->assertEquals($this->getSampleValues()['name']['family'], $author->getNameFamily());
    $this->assertEquals($this->getSampleValues()['country'], $author->getCountry());
    $this->assertEquals($this->getSampleValues()['org']['name'], $author->getOrgName());
    $this->assertEquals($this->getSampleValues()['org']['unit'], $author->getOrgUnit());
    $this->assertEquals($this->getSampleValues()['homepage'], $author->getHomepage());
    $this->assertEquals($this->getSampleValues()['description'], $author->getDescription());
    $this->assertEquals($this->getSampleValues()['image'], $author->getImage());
  }

  /**
   * Gets valid author ID.
   *
   * @return string
   *   The author ID.
   */
  public function getSampleId(): string {
    return 'bachman';
  }

  /**
   * Provides valid sample values.
   *
   * This done in dedicated method instead of property + setUp, because it is
   * used in data provider which called before setUp what leads to error.
   *
   * @return array
   *   An array with valid valies.
   */
  public function getSampleValues(): array {
    $directory = $this->setupFakeSourceDir();

    return [
      'name' => [
        'given' => 'Erlich',
        'family' => 'Bachman',
      ],
      'country' => 'US',
      'org' => [
        'name' => 'Pied Piper',
        'unit' => 'Landlord',
      ],
      'homepage' => 'http://www.piedpiper.com/',
      'description' => [
        'en' => 'Steve Jobs 2.0',
      ],
      'image' => $directory->url() . '/authors/image/dries.jpg',
    ];
  }

  /**
   * Tests that object detects invalid name.
   *
   * @dataProvider invalidValues
   */
  public function testInvalidValues($values): void {
    $this->expectException(\InvalidArgumentException::class);
    Author::createFromArray($this->getSampleId(), $values);
  }

  /**
   * Tests that object detects invalid ID.
   */
  public function testInvalidId(): void {
    $this->expectException(\InvalidArgumentException::class);
    Author::createFromArray('123 213', $this->getSampleValues());
  }

  /**
   * Data provides with wrong name values.
   */
  public function invalidValues(): array {
    $values = [];

    $values_set = $this->getSampleValues();
    unset($values_set['name']);
    $values['without name'] = [$values_set];

    $values_set = $this->getSampleValues();
    unset($values_set['name']['given']);
    $values['name without given'] = [$values_set];

    $values_set = $this->getSampleValues();
    unset($values_set['name']['family']);
    $values['name without family'] = [$values_set];

    $values_set = $this->getSampleValues();
    unset($values_set['country']);
    $values['missing country'] = [$values_set];

    $values_set = $this->getSampleValues();
    $values_set['country'] = 'USA';
    $values['invalid country code'] = [$values_set];

    $values_set = $this->getSampleValues();
    $values_set['org'] = 'test';
    $values['org is not an array'] = [$values_set];

    $values_set = $this->getSampleValues();
    unset($values_set['org']['name']);
    $values['missing org name'] = [$values_set];

    $values_set = $this->getSampleValues();
    unset($values_set['org']['unit']);
    $values['missing org unit'] = [$values_set];

    $values_set = $this->getSampleValues();
    $values_set['image'] = 'foo/bar.jpg';
    $values['invalid image'] = [$values_set];

    $values_set = $this->getSampleValues();
    $values_set['homepage'] = '/home';
    $values['invalid homepage'] = [$values_set];

    $values_set = $this->getSampleValues();
    $values_set['description']['UND'] = 'test';
    $values['invalid description langcode'] = [$values_set];

    $values_set = $this->getSampleValues();
    $values_set['description']['en'] = [];
    $values['invalid description'] = [$values_set];

    $values_set = $this->getSampleValues();
    $values_set['description'] = 'test';
    $values['invalid description value'] = [$values_set];

    return $values;
  }

}
