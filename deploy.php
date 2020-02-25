<?php

namespace Deployer;

require 'recipe/common.php';

set('repository', 'git@gitlab.com:druki/website.git');
set('drupal_site', 'default');
set('shared_dirs', [
  'web/sites/{{drupal_site}}/files',
]);
set('shared_files', [
  'web/sites/{{drupal_site}}/settings.php',
  'web/sites/{{drupal_site}}/services.yml',
]);
set('writable_dirs', [
  'web/sites/{{drupal_site}}/files',
]);

host('druki.ru')
  ->set('deploy_path', '~/web/druki.ru/private/deploy');

task('deploy:updatedb', function () {
  cd('{{deploy_path}}/current');
  run('drush updatedb -y');
});

task('deploy:config-split-export', function () {
  cd('{{deploy_path}}/current');
  run('drush config-split:export live -y');
});

task('deploy:config-split-import', function () {
  cd('{{deploy_path}}/current');
  run('drush config-split:export live -y');
});

task('deploy:config-import', function () {
  cd('{{deploy_path}}/current');
  writeln(run('drush config:import --diff -y'));
});

task('deploy:update-locale', function () {
  cd('{{deploy_path}}/current');
  run('drush locale:check');
  run('drush locale:update');
});

task('deploy:rebuild-cache', function () {
  cd('{{deploy_path}}/current');
  run('drush cache:rebuild -y');
});

task('deploy:minifyjs', function () {
  cd('{{deploy_path}}/current');
  run('drush scan-js');
  run('drush minify-js');
});

task('deploy:cache-warming', function () {
  cd('{{deploy_path}}/current');
  writeln(run('drush warmer:enqueue sitemap --run-queue'));
});

task('deploy', [
  'deploy:prepare',
  'deploy:lock',
  'deploy:release',
  'deploy:update_code',
  'deploy:shared',
  'deploy:writable',
  'deploy:vendors',
  'deploy:symlink',
  'deploy:updatedb',
  'deploy:config-split-export',
  'deploy:config-import',
  'deploy:config-split-import',
  'deploy:minifyjs',
  'deploy:rebuild-cache',
  'deploy:unlock',
  'deploy:cache-warming',
  'cleanup',
  'success',
]);

// If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
