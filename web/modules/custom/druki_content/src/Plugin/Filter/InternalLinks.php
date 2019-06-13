<?php

namespace Drupal\druki_content\Plugin\Filter;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\Handler\DrukiContentStorage;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a 'InternalLinks' filter.
 *
 * @Filter(
 *   id = "druki_content_internal_links",
 *   title = @Translation("Internal links"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 *   weight = -10
 * )
 */
class InternalLinks extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The dom crawler.
   *
   * @var \Symfony\Component\DomCrawler\Crawler
   */
  protected $crawler;

  /**
   * An array with cache tags for lazy re-render.
   *
   * @var array
   */
  protected $lazyCacheTags = [];

  /**
   * The druki content sotrage.
   *
   * @var \Drupal\druki_content\Handler\DrukiContentStorage
   */
  protected $drukiContentStorage;

  /**
   * The git settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $drukiGitSettings;

  /**
   * The repository path.
   *
   * @var array|null
   */
  protected $drukiGitRepositoryPath;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The relapath for git repository.
   *
   * @var false|string
   */
  protected $drukiGitRepositoryRealpath;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    DrukiContentStorage $druki_content_storage,
    ConfigFactoryInterface $config_factory,
    FileSystemInterface $file_system
  ) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->crawler = new Crawler();
    $this->drukiContentStorage = $druki_content_storage;
    $this->drukiGitSettings = $config_factory->get('druki_git.git_settings');
    $this->drukiGitRepositoryPath = $this->drukiGitSettings->get('repository_path');
    $this->fileSystem = $file_system;
    $this->drukiGitRepositoryRealpath = $this->fileSystem->realpath($this->drukiGitRepositoryPath);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): object {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('druki_content'),
      $container->get('config.factory'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $this->crawler->addContent($text);

    $replace_link = function ($node) {
      $original_href = $node->getNode(0)->getAttribute('href');
      // Initial value for all broken links.
      $destination_href = '#';
      $link_source_filepath = $node->getNode(0)->getAttribute('data-druki-internal-link-filepath');
      $source_realpath = $this->fileSystem->realpath($link_source_filepath);
      $source_dirname = dirname($source_realpath);
      // realpath() checks for file existence. So we wrap it to condition.
      // Also we add trailing slash after dirname for this contact:
      // - /path/to/source/dirname
      // - /
      // - ../path/to/href/file.md
      // Which will result as: /path/to/source/dirname/../path/to/href/file.md.
      // So we need this slash to work properly. If there will be double slash
      // realpath will work also.
      $destination_realpath = $this->normalizePath($source_dirname . '/' . $original_href);
      // Now we need to get "relative_pathname" for this file relative to
      // repository root.
      // We also remove leading slash from repository path. This is needed
      // because "relative_pathname" stored without it: docs/ru/file.md
      $destination_relative_pathname = str_replace($this->drukiGitRepositoryRealpath . '/', '', $destination_realpath);
      if (realpath($destination_realpath)) {
        // If we are here, this means the path is valid and file is exist.

        // Now we need to find the druki_content entity associated with this
        // relative pathname.
        $druki_content = $this->loadDrukiContentByRelativePathname($destination_relative_pathname);

        if ($druki_content instanceof DrukiContentInterface) {
          $destination_href = $druki_content
            ->toUrl()
            ->toString(TRUE)
            ->getGeneratedUrl();
        }
      }

      // @see Drupal\druki_content\Entity\DrukiContent::getCacheTagsToInvalidate();
      $relative_pathname_hash = Crypt::hashBase64($destination_relative_pathname);
      $this->addLazyCacheTag('druki_content:relative_pathname:' . $relative_pathname_hash);

      dump($destination_href);
      // Replace href value.
      $node->getNode(0)->setAttribute('href', $destination_href);
      $node->getNode(0)->removeAttribute('data-druki-internal-link-filepath');
    };

    // Dom Crawler not intended to change DOM, but small changes as ours is
    // possible.
    $this
      ->crawler
      ->filter('a[data-druki-internal-link-filepath]')
      ->each($replace_link);

    $text = $this->crawler->html();

    $result = new FilterProcessResult($text);
    // The main reason for this, is to handle links, which can be created at
    // particular moment, but can be later.
    // F.e. we have two content "Page 1" and "Page 2", they're imported
    // consecutive, but "Page 1" have internal link to "Page 2" content, but
    // this content can be new, and doesn't have actual entity on site. So, we
    // can't generate path for it. We add it cache tag to current result, and
    // when content will be created, it's invalidate tag, and this result will
    // be generated again, but at this time, "Page 2" will exist.
    // Maybe it's good place to use placeholder, but I think this technique is
    // will be more less CPU intended in long run. But who knows, we can rework
    // it if you prove it.
    $result->setCacheTags($this->lazyCacheTags);

    return $result;
  }

  /**
   * Normalize path like standard realpath() does, but ignore file existence.
   *
   * @param string $path
   *   The path to process. I.e. "path/to/something/../../file.md" will be
   *   convert to "path/file.md".
   *
   * @return string
   *   The path.
   *
   * @see https://stackoverflow.com/a/10067975/4751623
   */
  protected function normalizePath(string $path): string {
    $result_path = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $path, '');

    if ($result_path) {
      $root = ($path[0] === '/') ? '/' : '';

      $segments = explode('/', trim($path, '/'));
      $ret = [];
      foreach ($segments as $segment) {
        if (($segment == '.') || strlen($segment) === 0) {
          continue;
        }
        if ($segment == '..') {
          array_pop($ret);
        }
        else {
          array_push($ret, $segment);
        }
      }

      $result_path = $root . implode('/', $ret);
    }

    return $result_path;
  }

  /**
   * Loads content by relative_pathname value.
   *
   * @param string $relative_pathname
   *   The value for relative_pathname property.
   *
   * @return \Drupal\druki_content\Entity\DrukiContentInterface|null
   *   The content entity.
   */
  protected function loadDrukiContentByRelativePathname(string $relative_pathname): ?DrukiContentInterface {
    $result = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $relative_pathname);

    if (!isset($result)) {
      $content_ids = $this
        ->drukiContentStorage
        ->getQuery()
        ->condition('relative_pathname', $relative_pathname)
        ->range(0, 1)
        ->execute();

      if (!empty($content_ids)) {
        $content_id = array_shift($content_ids);
        $result = $this->drukiContentStorage->load($content_id);
      }
    }

    return $result;
  }

  /**
   * Adds lazy cache tag.
   *
   * @param string $cache_tag
   *   The cache tag.
   */
  protected function addLazyCacheTag(string $cache_tag): void {
    $this->lazyCacheTags[] = $cache_tag;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE): string {
    return $this->t('Convert internal links by relative paths to an actual aliases.');
  }

}
