#!/bin/bash
# Script for deployment operations.
#
# Available variables:
# - USER: The username for server.
# - HOST: The hostname for server.
# - PROJECT_ROOT: The root path of the project relative to USER home.
# There are predifined variables exist as well: https://docs.gitlab.com/ee/ci/variables/predefined_variables.html

# Connect to server and run deploy pipeline.
ssh "$USER"@"$HOST" <<EOF
  # Navigates to project root.
  cd $PROJECT_ROOT;
  # Disable old theme.
  drush pmu roachl
  # Fetch project from repository to log changes.
  git fetch && git checkout;
  # Force to fix write permission to directory. Sometimes it mess up after
  # composer update.
  chmod +w ./web/sites/default;
  # Pull new data.
  git pull origin $CI_DEFAULT_BRANCH;
  git checkout $CI_COMMIT_TAG;
  # Install dependencies.
  composer install --no-dev -o -n;
  # Update Drupal database.
  drush updatedb -y;
  # Export config_split changes.
  #drush config-split:export live -y;
  # Import configuration changes with diff output for logs.
  drush config:import --diff -y;
  # Import config_split back.
  #drush config-split:import live -y;
  # Check for localization updates.
  drush locale:check;
  # Apply localization updates.
  drush locale:update;
  # Clear the cache.
  drush cache:rebuild -y;
EOF
