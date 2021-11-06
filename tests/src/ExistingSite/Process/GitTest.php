<?php

declare(strict_types=1);

namespace Druki\Tests\ExistingSite\Process;

use Drupal\druki\Process\GitInterface;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Provides test for testing Git process.
 *
 * @coversDefaultClass \Drupal\druki\Process\Git
 */
final class GitTest extends ExistingSiteBase {

  /**
   * The git process.
   */
  protected GitInterface $git;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->git = $this->container->get('druki.process.git');
  }

  /**
   * Tests process creation for pulling from repository
   */
  public function testPull(): void {
    $process = $this->git->pull('/foo/bar');
    $this->assertEquals("'git' 'pull' '--ff-only'", $process->getCommandLine());
    $this->assertEquals('/foo/bar', $process->getWorkingDirectory());
  }

  /**
   * Tests process creation for getting last commit ID.
   */
  public function testGetLastCommitId(): void {
    $process = $this->git->getLastCommitId('/foo/bar');
    $this->assertEquals("'git' 'log' '--format=\"%H\"' '-n 1'", $process->getCommandLine());
    $this->assertEquals('/foo/bar', $process->getWorkingDirectory());
  }

  /**
   * Tests process creation for getting last commit ID of the file.
   */
  public function testGetFileLastCommitId(): void {
    $process = $this->git->getFileLastCommitId('/foo/bar', 'baz/index.md');
    $this->assertEquals("'git' 'log' '--format=\"%H\"' '-n 1' '--' 'baz/index.md'", $process->getCommandLine());
    $this->assertEquals('/foo/bar', $process->getWorkingDirectory());
  }

  /**
   * Tests process creation for getting file contributors.
   */
  public function testGetFileContributors(): void {
    $process = $this->git->getFileContributors('/foo/bar', 'baz/index.md');
    $this->assertEquals("'git' 'shortlog' '--summary' '--email' '--numbered' '--' 'baz/index.md'", $process->getCommandLine());
    $this->assertEquals('/foo/bar', $process->getWorkingDirectory());
  }

}
