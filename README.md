# Terminus Plugin: Replica

Replica is a Terminus plugin that takes over the core `site connection-info`
command, appending database replica connection info when available.

## Usage

If you pull connection info for an environment that has an associated slave
database, you will see additional details:

```sh
terminus site --site=my-site --env=live connection-info
```

...Which might respond with:

```
+------------------------+--------------------------------------------------------------------------+
| Key                    | Value                                                                    |
+------------------------+--------------------------------------------------------------------------+
| Sftp Username          | live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij                                |
| Sftp Host              | appserver.live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij.drush.in             |
| Sftp Port              | 2222                                                                     |
| Sftp Password          | Use your account password                                                |
| Sftp Url               | sftp://live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij@appserver.live.aaaaaaaa |
|                        | -0000-bbbb-1111-cdef2345ghij.drush.in:2222                               |
| Sftp Command           | sftp -o Port=2222 live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij@appserver.li |
|                        | ve.aaaaaaaa-0000-bbbb-1111-cdef2345ghij.drush.in                         |
| Mysql Host             | dbserver.live.aaaaaaaa-0000-bbbb-1111-cdef2345ghij.drush.in              |
| Mysql Username         | pantheon                                                                 |
| Mysql Password         | 12a345b678cd9012efgh345i67890123                                         |
| Mysql Port             | 10206                                                                    |
| Mysql Database         | pantheon                                                                 |
| Mysql Url              | mysql://pantheon:12a345b678cd9012efgh345i67890123@dbserver.live.aaaaaaaa |
|                        | -0000-bbbb-1111-cdef2345ghij.drush.in:10206/pantheon                     |
| Mysql Command          | mysql -u pantheon -p12a345b678cd9012efgh345i67890123 -h dbserver.live.aa |
|                        | aaaaaa-0000-bbbb-1111-cdef2345ghij.drush.in -P 10206 pantheon            |
| Redis Password         | aa1b23456cde789fgh0ij1234k56l7m8                                         |
| Redis Host             | 13.37.13.37                                                              |
| Redis Port             | 10000                                                                    |
| Redis Url              | redis://pantheon:dd8c29243bdf456baf7ca9854c95a2f8@13.37.13.37:10000      |
| Redis Command          | redis-cli -h 13.37.13.37 -p 10000 -a aa1b23456cde789fgh0ij1234k56l7m8    |
| Mysql Replica Host     | 133.71.33.7                                                              |
| Mysql Replica Username | pantheon                                                                 |
| Mysql Replica Password | 01ab234c567d8e90fgh1i23j4kl5678m                                         |
| Mysql Replica Port     | 11258                                                                    |
| Mysql Replica Database | pantheon                                                                 |
| Mysql Replica Url      | mysql://pantheon:01ab234c567d8e90fgh1i23j4kl5678m@133.71.33.7:11258/p    |
|                        | antheon                                                                  |
| Mysql Replica Command  | mysql -u pantheon -p01ab234c567d8e90fgh1i23j4kl5678m -h 133.71.33.7 -P   |
|                        | 11258 pantheon                                                           |
+------------------------+--------------------------------------------------------------------------+
```

And, as usual, you can specify the field you want:

```sh
terminus site --site=my-site --env=live connection-info --field=mysql_replica_command
```

...Which might return:

```
mysql -u pantheon -p01ab234c567d8e90fgh1i23j4kl5678m -h 133.71.33.7 -P 11258 pantheon
```
