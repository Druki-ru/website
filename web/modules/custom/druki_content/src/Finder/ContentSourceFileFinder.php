<?php

namespace Drupal\druki_content\Finder;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\druki\Finder\MarkdownDirectoryFinder;
use Drupal\druki_content\Data\ContentSourceFile;
use Drupal\druki_content\Data\ContentSourceFileList;

/**
 * Provides finder for source content files.
 *
 * Source folder always must follow this structure:
 *
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
final class ContentSourceFileFinder {

  /**
   * The language manager.
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * Constructs a new ContentSourceFileFinder object.
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
   * @return \Drupal\druki_content\Data\ContentSourceFileList
   *   Source content list.
   */
  public function findAll(string $directory): ContentSourceFileList {
    $all = new ContentSourceFileList();
    // Source directory must contain "/docs" folder.
    $docs_folder = 'docs';

    $active_languages = $this->languageManager->getLanguages();
    $active_langcodes = \array_keys($active_languages);

    // Find all source content grouped by langcode.
    foreach ($active_langcodes as $langcode) {
      $dir_path = \implode(\DIRECTORY_SEPARATOR, [$directory, $docs_folder, $langcode]);
      $finder = new MarkdownDirectoryFinder([$dir_path]);
      foreach ($finder->findAll() as $file) {
        $relative_pathname = \implode(\DIRECTORY_SEPARATOR, [$docs_folder, $langcode, $file->getRelativePathname()]);
        $all->addFile(new ContentSourceFile($file->getPathname(), $relative_pathname, $langcode));
      }
    }

    return $all;
  }

}
