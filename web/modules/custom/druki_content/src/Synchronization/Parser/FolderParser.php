<?php

namespace Drupal\druki_content\Synchronization\Parser;

use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\Finder\Finder;

/**
 * Parser for filesystem folders.
 *
 * This parser scans provided folder for specific structure:
 * - docs
 * -- LANGCODE - looking only for languages available on site. Folder must be
 *    the same name as langcode in Drupal.
 * --- All other files.
 *
 * @deprecated In flavor of \Drupal\druki_content\Finder\SourceContentFinder
 */
class FolderParser implements FolderParserInterface {

  /**
   * The finder component.
   *
   * @var \Symfony\Component\Finder\Finder
   */
  protected $finder;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * DrukiFolderParser constructor.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->finder = new Finder();

    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function parse($directory): array {
    $this->finder->in($directory);
    $this->finder->name('*.md');
    $this->finder->name('*.MD');

    $active_languages = $this->languageManager->getLanguages();
    $active_langcodes = array_keys($active_languages);

    $parsed_files = [];

    foreach ($this->finder as $file_info) {
      // Check if path any of enabled languages.
      foreach ($active_langcodes as $langcode) {
        if (preg_match("/docs\/{$langcode}.*?/i", $file_info->getRelativePath())) {
          $parsed_files[$langcode][] = $file_info;
        }
      }
    }

    return $parsed_files;
  }

}
