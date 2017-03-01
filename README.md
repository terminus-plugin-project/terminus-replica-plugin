# Terminus Plugin: Replica

Replica is a Terminus plugin that decorates the terminus `connection:info`
command with additional connection info related to any database replicas you
may have enabled for your site environments.

## Installation
For installation help, see [Manage Plugins](https://pantheon.io/docs/terminus/plugins/).

```sh
mkdir -p ~/.terminus/plugins
composer create-project -d ~/.terminus/plugins terminus-plugin-project/terminus-replica-plugin:~1
```

## Usage

If you pull connection info for an environment that has an associated slave
database, you will see additional details:

```sh
terminus connection:info <site>.<env>
```

...Which might respond with:

```
 ----------------------- ---------------------------------------------------------------------------------------------------------------------------------------
  SFTP Command            sftp -o Port=2222 live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij@appserver.live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij.drush.in
  MySQL Command           mysql -u pantheon -p12a345b678cd9012efgh345i67890123 -h dbserver.live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij.drush.in -P 10206 pantheon
  Redis Command           redis-cli -h 13.37.13.37 -p 10000 -a aa1b23456cde789fgh0ij1234k56l7m8
  MySQL Replica Command   mysql -u pantheon -p01ab234c567d8e90fgh1i23j4kl5678m -h 133.71.33.7 -P 11258 pantheon
 ----------------------- ---------------------------------------------------------------------------------------------------------------------------------------
```

And, as usual, you can specify the field you want:

```sh
terminus connection:info my-site.live --field=mysql_replica_command
```

...Which might return:

```
mysql -u pantheon -p01ab234c567d8e90fgh1i23j4kl5678m -h 133.71.33.7 -P 11258 pantheon
```
