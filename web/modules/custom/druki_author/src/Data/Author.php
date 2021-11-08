<?php

declare(strict_types=1);

namespace Drupal\druki_author\Data;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Locale\CountryManager;

/**
 * Provides author value object.
 */
final class Author {

  /**
   * The author ID.
   */
  protected string $id;

  /**
   * The author given name.
   */
  protected string $nameGiven;

  /**
   * The author family name.
   */
  protected string $nameFamily;

  /**
   * The author country in ISO 3166-1 alpha-2 format.
   */
  protected string $country;

  /**
   * The author organization name.
   */
  protected ?string $orgName = NULL;

  /**
   * The author organization unit.
   */
  protected ?string $orgUnit = NULL;

  /**
   * The author homepage.
   */
  protected ?string $homepage = NULL;

  /**
   * An array with descriptions keyed by langcode.
   */
  protected array $description = [];

  /**
   * The realpath for author image.
   */
  protected ?string $image = NULL;

  /**
   * Builds an instance from an array.
   *
   * @param string $id
   *   The author ID.
   * @param array $values
   *   The author information.
   *
   * @return self
   */
  public static function createFromArray(string $id, array $values): self {
    $instance = new self();
    if (!\preg_match('/^[a-z0-9]{1,64}$/', $id)) {
      throw new \InvalidArgumentException('Author ID contains not allowed characters, please fix it.');
    }
    $instance->id = $id;

    if (!isset($values['name']) || !\is_array($values['name'])) {
      throw new \InvalidArgumentException("The 'name' value is missing or incorrect.");
    }
    if (\array_diff(['given', 'family'], \array_keys($values['name']))) {
      throw new \InvalidArgumentException("Author name should contains 'given' and 'family' values.");
    }
    $instance->nameGiven = $values['name']['given'];
    $instance->nameFamily = $values['name']['family'];

    if (!isset($values['country'])) {
      throw new \InvalidArgumentException("Missing required value 'country'.");
    }
    $country_list = \array_keys(CountryManager::getStandardList());
    if (!\in_array($values['country'], $country_list)) {
      throw new \InvalidArgumentException('Country value is incorrect. It should be valid ISO 3166-1 alpha-2 value.');
    }
    $instance->country = $values['country'];

    if (isset($values['org'])) {
      if (!\is_array($values['org'])) {
        throw new \InvalidArgumentException('Organization value should be an array.');
      }
      if (\array_diff(['name', 'unit'], \array_keys($values['name']))) {
        throw new \InvalidArgumentException("Author name should contains 'given' and 'family' values.");
      }
      $instance->orgName = $values['org']['name'];
      $instance->orgUnit = $values['org']['unit'];
    }

    if (isset($values['homepage'])) {
      if (!UrlHelper::isValid($values['homepage']) || !UrlHelper::isExternal($values['homepage'])) {
        throw new \InvalidArgumentException('Homepage must be valid external URL.');
      }
      $instance->homepage = $values['homepage'];
    }

    if (isset($values['description'])) {
      if (!\is_array($values['description'])) {
        throw new \InvalidArgumentException('The description should be an array with descriptions keyed by a language code.');
      }
      $allowed_languages = \array_keys(LanguageManager::getStandardLanguageList());
      $provided_languages = \array_keys($values['description']);
      if (\array_diff($provided_languages, $allowed_languages)) {
        throw new \InvalidArgumentException('The descriptions should be keyed by a valid language code.');
      }
      foreach ($values['description'] as $langcode => $description) {
        if (!\is_string($description)) {
          throw new \InvalidArgumentException('Description should be a string.');
        }
        $instance->description[$langcode] = $description;
      }
    }

    if (isset($values['image'])) {
      if (!\file_exists($values['image'])) {
        throw new \InvalidArgumentException('The image URI is incorrect.');
      }
      $instance->image = $values['image'];
    }

    return $instance;
  }

  /**
   * Gets author ID.
   *
   * @return string
   *   The author ID.
   */
  public function getId(): string {
    return $this->id;
  }

  /**
   * Gets family name.
   *
   * @return string
   *   The family author name.
   */
  public function getNameFamily(): string {
    return $this->nameFamily;
  }

  /**
   * Gets given name.
   *
   * @return string
   *   The given author name.
   */
  public function getNameGiven(): string {
    return $this->nameGiven;
  }

  /**
   * Gets author country code.
   *
   * @return string
   *   The country code.
   */
  public function getCountry(): string {
    return $this->country;
  }

  /**
   * Gets author organization name.
   *
   * @return string|null
   *   The organization name.
   */
  public function getOrgName(): ?string {
    return $this->orgName;
  }

  /**
   * Gets author organization unit.
   *
   * @return string|null
   *   The organization unit.
   */
  public function getOrgUnit(): ?string {
    return $this->orgUnit;
  }

  /**
   * Gets authors homepage URL.
   *
   * @return string|null
   *   The URL.
   */
  public function getHomepage(): ?string {
    return $this->homepage;
  }

  /**
   * Gets author descriptions.
   *
   * @return array
   *   An array with descriptions keyed by langcode.
   */
  public function getDescription(): array {
    return $this->description;
  }

  /**
   * Gets author image.
   *
   * @return string|null
   *   The realpath to image.
   */
  public function getImage(): ?string {
    return $this->image;
  }

}
