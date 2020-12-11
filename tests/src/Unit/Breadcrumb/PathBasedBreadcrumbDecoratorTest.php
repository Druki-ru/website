<?php

namespace Drupal\Tests\druki\Unit\Breadcrumb;

use Drupal\Core\Access\AccessManagerInterface;
use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Cache\Context\CacheContextsManager;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Link;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\RouteObjectInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\druki\Breadcrumb\PathBasedBreadcrumbDecorator;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides decorator test.
 *
 * @coversDefaultClass \Drupal\druki\Breadcrumb\PathBasedBreadcrumbDecorator
 */
final class PathBasedBreadcrumbDecoratorTest extends UnitTestCase {

  /**
   * The breadcrumb builder.
   *
   * @var \Drupal\druki\Breadcrumb\PathBasedBreadcrumbDecorator
   */
  protected $builder;

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $requestContext;

  /**
   * The path processor.
   *
   * @var \Drupal\Core\PathProcessor\InboundPathProcessorInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $pathProcessor;

  /**
   * The request matcher.
   *
   * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Routing\Matcher\RequestMatcherInterface
   */
  protected $requestMatcher;

  /**
   * The access manager.
   *
   * @var \Drupal\Core\Access\AccessManagerInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $accessManager;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $currentPath = $this->getMockBuilder(CurrentPathStack::class)
      ->disableOriginalConstructor()
      ->getMock();

    $this->requestContext = $this->createMock(RequestContext::class);
    $this->accessManager = $this->createMock(AccessManagerInterface::class);
    $this->pathProcessor = $this->createMock(InboundPathProcessorInterface::class);
    $this->requestMatcher = $this->createMock(RequestMatcherInterface::class);

    $this->builder = new PathBasedBreadcrumbDecorator(
      $this->requestContext,
      $this->accessManager,
      $this->requestMatcher,
      $this->pathProcessor,
      $this->getConfigFactoryStub(['system.site' => ['front' => 'test_frontpage']]),
      $this->createMock(TitleResolverInterface::class),
      $this->createMock(AccountInterface::class),
      $currentPath,
      $this->createMock(PathMatcherInterface::class)
    );

    $this->builder->setStringTranslation($this->getStringTranslationStub());

    $cache_contexts_manager = $this->getMockBuilder(CacheContextsManager::class)
      ->disableOriginalConstructor()
      ->getMock();
    $cache_contexts_manager->method('assertValidTokens')->willReturn(TRUE);

    $container = new Container();
    $container->set('cache_contexts_manager', $cache_contexts_manager);
    \Drupal::setContainer($container);
  }

  /**
   * Test that wiki route link has specific title.
   *
   * @covers ::build
   */
  public function testWikiTitleDecoration(): void {
    $this->requestContext->expects($this->once())
      ->method('getPathInfo')
      ->willReturn('/wiki/test');

    $this->pathProcessor->expects($this->any())
      ->method('processInbound')
      ->willReturnArgument(0);

    $route_wiki = new Route('/wiki');
    $this->requestMatcher->expects($this->exactly(1))
      ->method('matchRequest')
      ->willReturnCallback(function (Request $request) use ($route_wiki) {
        if ($request->getPathInfo() == '/wiki') {
          return [
            RouteObjectInterface::ROUTE_NAME => 'druki.wiki',
            RouteObjectInterface::ROUTE_OBJECT => $route_wiki,
            '_raw_variables' => new ParameterBag([]),
          ];
        }
      });

    $this->accessManager->expects($this->any())
      ->method('check')
      ->willReturn((new AccessResultAllowed())->cachePerPermissions());

    $breadcrumb = $this->builder->build($this->createMock(RouteMatchInterface::class));
    $breadcrumb_links = $breadcrumb->getLinks();
    $this->assertEquals(new Link('Home', new Url('<front>')), $breadcrumb_links[0]);
    $this->assertEquals(new Link(new TranslatableMarkup('Wiki'), new Url('druki.wiki')), $breadcrumb_links[1]);
  }

}
