<?php

namespace Terminus\Commands;

use Terminus\Models\Binding;

/**
 * Class SlightlyDecoratedCommand
 * @command site
 */
class SlightlyDecoratedCommand extends SiteCommand {

  /**
   * Retrieve connection info for a specific environment
   * e.g. git, sftp, mysql, redis, and mysql replica.
   *
   * ## OPTIONS
   *
   * [--site=<site>]
   * : Name of the site from which to fetch connection info
   *
   * [--env=<env>]
   * : Name of the specific environment from which to fetch connection info
   *
   * [--field=<field>]
   * : Name of a specfic field in the connection info to return
   *   Choices are git_command, git_host, git_port, git_url, git_username,
   *   mysql_command, mysql_database, mysql_host, mysql_password, mysql_port,
   *   mysql_url, mysql_username, mysql_replica_command, mysql_replica_database,
   *   mysql_replica_host, mysql_replica_password, mysql_replica_port,
   *   mysql_replica_url, mysql_replica_username, redis_command, redis_password,
   *   redis_port, redis_url, sftp_command, sftp_host, sftp_password, sftp_url,
   *   and sftp_username
   *
   * @subcommand connection-info
   */
  public function connectionInfo($args, $assoc_args) {
    $site = $this->sites->get(
      $this->input()->siteName(array('args' => $assoc_args))
    );
    $env_id = $this->input()->env(array('args' => $assoc_args, 'site' => $site));
    $environment = $site->environments->get($env_id);
    $info = $environment->connectionInfo();

    $dbservers = array_filter($environment->bindings->all(), function(Binding $binding) use ($environment) {
      return $binding->get('environment') == $environment->id
        && $binding->get('type') === 'dbserver'
        && $binding->get('slave_of');
    });

    if (!empty($dbservers)) {
      $replica_info = $this->interpretBinding(array_shift($dbservers));
      foreach ($replica_info as $field => $value) {
        $info['mysql_replica_' . $field] = $value;
      }
    }

    if (isset($assoc_args['field'])) {
      $field = $assoc_args['field'];
      $this->output()->outputValue($info[$field]);
    } else {
      $this->output()->outputRecord($info);
    }
  }

  /**
   * @param Binding $dbServer
   */
  protected function interpretBinding(Binding $dbServer) {
    $username = 'pantheon';
    $password = $dbServer->get('password');
    $hostname = $dbServer->get('host');
    $port = $dbServer->get('port');
    $database = 'pantheon';
    $url = "mysql://$username:$password@$hostname:$port/$database";
    $command  = "mysql -u $username -p$password -h $hostname -P $port $database";

    return array(
      'host' => $hostname,
      'username' => $username,
      'password' => $password,
      'port' => $port,
      'database' => $database,
      'url' => $url,
      'command' => $command,
    );
  }

}
