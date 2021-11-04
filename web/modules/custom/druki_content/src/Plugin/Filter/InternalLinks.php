<?php

namespace Drupal\druki_content\Plugin\Filter;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\druki\Utility\PathUtils;
use Drupal\druki_content\Entity\DrukiContentInterface;
use Drupal\druki_content\Repository\DrukiContentStorage;
use Drupal\druki_git\Git\GitSettingsInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a 'InternalLinks' filter.
 *
 * This filter converts internal links in content (between files) into real one.
 *
 * E.g., we have such link in content:
 *
 * @code
 * <a href='services/services.md'
 *   data-druki-internal-link-filepath='public://druki-content-source/docs/ru/8/settings-php.md'>Service API</a>
 * @endcode
 *
 * The website cannot open 'service/services.md' link, but if this document is
 * exists, that we know it's entity and by that, we know it's real URL.
 *
 * This filter will find such links and replace document-to-document links into
 * internal one:
 *
 * @code
 * <a href="/wiki/8/services">Service API</a>
 * @endcode
 *
 * @Filter(
 *   id = "druki_content_internal_links",
 *   title = @Translation("Internal links"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 *   weight = -10
 * )
 */
final class InternalLinks extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * An array with cache tags for lazy re-render.
   */
  protected array $lazyCacheTags = [];

  /**
   * The druki content storage.
   */
  protected DrukiContentStorage $contentStorage;

  /**
   * The git settings.
   */
  protected GitSettingsInterface $gitSettings;

  /**
   * The file system.
   */
  protected FileSystemInterface $fileSystem;

  /**
   * The cacke backend.
   */
  protected CacheBackendInterface $cache;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $druki_content_storage = $container->get('entity_type.manager')
      ->getStorage('druki_content');
    \assert($druki_content_storage instanceof DrukiContentStorage);
    $instance->contentStorage = $druki_content_storage;
    $instance->gitSettings = $container->get('druki_git.settings');
    $instance->fileSystem = $container->get('file_system');
    $instance->cache = $container->get('cache.static');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $result = new FilterProcessResult($text);

    // Do not run processing if text doesn't contains this value.
    if (\stristr($text, 'data-druki-internal-link-filepath') === FALSE) {
      return $result;
    }

    $repository_realpath = $this->fileSystem->realpath($this->gitSettings->getRepositoryPath());

    $crawler = new Crawler($text);
    $internal_links = $crawler->filter('a[data-druki-internal-link-filepath]');
    /** @var \DOMElement $internal_link */
    foreach ($internal_links as $internal_link) {
      // Initial value for all broken links.
      $destination_href = '#';
      $original_href = $internal_link->getAttribute('href');
      $link_source_filepath = $internal_link->getAttribute('data-druki-internal-link-filepath');

      $source_realpath = $this->fileSystem->realpath($link_source_filepath);
      $source_dirname = \dirname($source_realpath);

      $destination_realpath = PathUtils::normalizePath($source_dirname . '/' . $original_href);
      // Find "relative_pathname" for this file, related to repository root.
      //
      // E.g:
      // - $repository_realpath: /var/www/content
      // - $destination_realpath: /var/www/content/docs/ru/drupal/index.md
      // - $destination_relative_pathname: docs/ru/drupal/index.md
      //
      // We also remove leading slash (/) from repository path. This is needed
      // because "relative_pathname" stored in entity without it.
      $destination_relative_pathname = \str_replace($repository_realpath . '/', '', $destination_realpath);

      $druki_content = $this->loadDrukiContentByRelativePathname($destination_relative_pathname);
      if ($druki_content instanceof DrukiContentInterface) {
        $destination_href = $druki_content
          ->toUrl()
          ->toString(TRUE)
          ->getGeneratedUrl();
      }

      // @see Drupal\druki_content\Entity\DrukiContent::getCacheTagsToInvalidate();
      $relative_pathname_hash = Crypt::hashBase64($destination_relative_pathname);
      $this->addLazyCacheTag('druki_content:relative_pathname:' . $relative_pathname_hash);

      // Replace href value.
      $internal_link->setAttribute('href', $destination_href);
      $internal_link->removeAttribute('data-druki-internal-link-filepath');
    }

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
    $result->setProcessedText($crawler->filter('body')->html());

    return $result;
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
    $cid = self::class . ':' . __METHOD__ . ':' . $relative_pathname;
    if ($cache = $this->cache->get($cid)) {
      return $cache->data;
    }
    else {
      $content_ids = $this
        ->contentStorage
        ->getQuery()
        ->condition('relative_pathname', $relative_pathname)
        ->range(0, 1)
        ->execute();

      $result = NULL;
      if (!empty($content_ids)) {
        $content_id = \array_shift($content_ids);
        $result = $this->contentStorage->load($content_id);
      }
      $this->cache->set($cid, $result);
      return $result;
    }
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
