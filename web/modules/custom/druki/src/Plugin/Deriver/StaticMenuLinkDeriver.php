<?php

declare(strict_types=1);

namespace Drupal\druki\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a deriver for static menu links.
 */
final class StaticMenuLinkDeriver extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    $this->addStaticMenuLink(
      'frontpage',
      'main',
      new TranslatableMarkup('Frontpage', [], ['context' => 'short']),
      '<front>',
      1,
    );
    $this->addStaticMenuLink(
      'frontpage',
      'mobile',
      new TranslatableMarkup('Frontpage', [], ['context' => 'short']),
      '<front>',
      1,
    );

    $this->addStaticMenuLink('wiki',
      'main',
      new TranslatableMarkup('Wiki'),
      'druki.wiki',
      2,
    );
    $this->addStaticMenuLink(
      'wiki',
      'mobile',
      new TranslatableMarkup('Wiki'),
      'druki.wiki',
      2,
    );

    $this->addStaticMenuLink(
      'download',
      'main',
      new TranslatableMarkup('Download Drupal'),
      'druki.download',
      3,
    );
    $this->addStaticMenuLink(
      'download',
      'mobile',
      new TranslatableMarkup('Download Drupal'),
      'druki.download',
      3,
    );

    return $this->derivatives;
  }

  /**
   * Adds menu link derivative.
   *
   * @param string $id
   *   The menu item ID.
   * @param string $menu_name
   *   The menu name to add link to.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $title
   *   The link title.
   * @param string $route_name
   *   The link route name.
   * @param int $weight
   *   The link weight in menu.
   */
  protected function addStaticMenuLink(string $id, string $menu_name, TranslatableMarkup $title, string $route_name, int $weight = 0): void {
    $this->derivatives["druki.{$id}.{$menu_name}"] = [
      'title' => $title,
      'route_name' => $route_name,
      'menu_name' => $menu_name,
      'weight' => $weight,
    ];
  }

}
