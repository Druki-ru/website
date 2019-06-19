<?php

namespace Drupal\druki\Service;

use GuzzleHttp\Client;
use SimpleXMLElement;
use XMLReader;

/**
 * Class DrupalProjects.
 *
 * @todo maybe replace or use UpdateFetcher, since there is the same code lol.
 *
 * This service is helps to find some project information.
 */
class DrupalProjects {

  /**
   * Base URL for projects API endpoint.
   */
  const PROJECT_API_BASE_URL = 'https://updates.drupal.org/release-history';

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The XML reader.
   *
   * @var \XMLReader
   */
  protected $xmlReader;

  /**
   * DrupalProjects constructor.
   *
   * @param \GuzzleHttp\Client $httpClient
   */
  public function __construct(Client $httpClient) {
    $this->httpClient = $httpClient;
    $this->xmlReader = new XMLReader();
  }

  /**
   * Gets project last stable release.
   *
   * @param string $project_name
   *   The project name.
   * @param string $api_compatibility
   *   The project API compatibility. E.g. "8.x".
   *
   * @return string|null
   *   The last stable release version, NULL if something wrong happens or
   *   stable release is missing.
   */
  public function getProjectLastStableRelease(string $project_name, string $api_compatibility): ?string {
    $releases = $this->getProjectReleases($project_name, $api_compatibility);

    if (!empty($releases)) {
      foreach ($releases as $release) {
        if ($release['stable']) {
          return $release['version'];
        }
      }
    }

    return NULL;
  }

  /**
   * Gets project releases.
   *
   * @param string $project_name
   *   The project name.
   * @param string $api_compatibility
   *   The project API compatibility. E.g. "8.x".
   *
   * @return array
   *   An array with releases info for provided project.
   */
  public function getProjectReleases(string $project_name, string $api_compatibility): array {
    $releases = &drupal_static(__CLASS__ . ':' . __METHOD__ . ':' . $project_name . ':' . $api_compatibility);

    if (!isset($releases)) {
      // Set default result if there is something wrong happens on the way.
      $releases = [];
      $uri = $this::PROJECT_API_BASE_URL . '/' . $project_name . '/' . $api_compatibility;

      $response = $this->httpClient->get($uri);

      if ($response->getStatusCode() == 200) {
        $content = $response->getBody()->getContents();
        $this->xmlReader->XML($content);

        // Find <releases>.
        while ($this->xmlReader->read() && $this->xmlReader->name !== 'releases') {

        }

        if ($this->xmlReader->name == 'releases') {
          $releases_dom = new SimpleXMLElement($this->xmlReader->readOuterXml());

          /** @var SimpleXMLElement $element */
          foreach ($releases_dom as $element) {
            $item = [
              'version' => (string) $element->version,
              'stable' => $element->version_extra ? FALSE : TRUE,
            ];

            $releases[] = $item;
          }
        }
      }
    }

    return $releases;
  }

}
