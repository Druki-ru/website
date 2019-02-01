# Druki - Git

This module working with git repository. The current usage is to check, is there active repository and pulling actual data from it.

## Troubleshooting

### Git library can't access private repository.

The best way to fix it, is create SSH key without password and add it to repo.

If you are using it on localhost with Docker4Drupal, better to generate key in different place and then mount it to PHP container.

1. `ssh-keygen -t rsa -b 4096 -C "your_email@example.com"`
2. Enter path to key something like that: `/home/USERNAME/.ssh/druki/id_rsa`. Don't replace your current key accidentally.
3. Edit docker-compose.yml

```yaml
  php:
    volumes:
      - ~/.ssh/druki:/home/wodby/.ssh
```

4. Then restart containers `make`.

First time you must clone repo or pull it with this key manually, to add record in known_hosts.

1. `docker-compose exec php sh`
2. `cd /path/to/repository`
3. `git pull` and answer yes.

For more information read their [docs](https://github.com/wodby/php#sshd).
