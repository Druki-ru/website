<?php

namespace Drupal\druki_content\Finder;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\druki\Finder\MarkdownDirectoryFinder;

/**
 * Provides finder for source content files.
 *
 * Source folder always must follow this structure:
 * @code
 * source/
 * └─ docs/
 *   ├─ ru/
 *   └─ en/
 * @endcode
 *
 * The "docs" folder leaded by langcode-named folder is mandatory. This means
 * content must be provided for at least one language.
 *
 * This implementation search only for active languages on current Drupal
 * installation. Other languages will be ignored.
 */
final class SourceContentFinder {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new SourceContentFinder object.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * Search content sources in directory.
   *
   * @param string $directory
   *   The directory path with sources.
   *
   * @return array
   *   An array with found sources, keyed by pathname and grouped by langcode.
   *   E.g.:
   *   [
   *     'ru' => [
   *       'public://content-source/docs/ru/drupal.md' => 'drupal.md',
   *     ],
   *   ],
   */
  public function findAll(string $directory) {
    $all = [];
    // Source directory must contain "/docs" folder.
    $directory .= '/docs';

    $active_languages = $this->languageManager->getLanguages();
    $active_langcodes = array_keys($active_languages);

    // Find all source content grouped by langcode.
    foreach ($active_langcodes as $langcode) {
      $finder = new MarkdownDirectoryFinder(["{$directory}/{$langcode}"]);
      $all[$langcode] = $finder->findAll();
    }

    return $all;
  }

}
