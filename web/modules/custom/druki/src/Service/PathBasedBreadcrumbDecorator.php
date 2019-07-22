<?php

namespace Drupal\druki\Service;

use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\system\PathBasedBreadcrumbBuilder;

/**
 * Alter a bit PathBasedBreadcrumbBuilder.
 */
class PathBasedBreadcrumbDecorator extends PathBasedBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = parent::build($route_match);

    foreach ($breadcrumb->getLinks() as $link) {
      if ($link->getUrl()->getRouteName() != 'druki.wiki') {
        continue;
      }

      $link->setText($this->t('Wiki'));
    }

    return $breadcrumb;
  }

}
