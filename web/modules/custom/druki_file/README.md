# Druki - Files

This module extends API for File entity type:

- Tracking all files with their MD5 hash.

All this helps to reduce duplicate files on the site and duplicate entities.

Since images can be added from anywhere: Drupal UI, git repository, remote url in content, and we loads them all to store locally, we need to detect duplicates to reduce a lot of "dead" files. Since druki_content load images every time, this is needed to check, do we actually need to load particular file, or we have it already and can just reuse it.
