<?php

namespace Pantheon\Replica\Commands;

use Consolidation\AnnotatedCommand\AnnotationData;
use Consolidation\AnnotatedCommand\CommandData;
use Consolidation\AnnotatedCommand\Hooks\AlterResultInterface;
use Consolidation\AnnotatedCommand\Hooks\OptionHookInterface;
use Consolidation\OutputFormatters\StructuredData\PropertyList;
use Pantheon\Terminus\Site\SiteAwareInterface;
use Pantheon\Terminus\Site\SiteAwareTrait;
use Pantheon\Terminus\Models\Binding;
use Symfony\Component\Console\Command\Command;


/**
 * Class ReplicaCommand
 * @package Pantheon\Replica\Commands
 */
class ReplicaCommand implements AlterResultInterface, OptionHookInterface, SiteAwareInterface {

  use SiteAwareTrait;

  /**
   * Ensures replica connection info is printed by the connection:info command.
   *
   * @param Command $command
   * @param AnnotationData $annotationData
   *
   * @hook option connection:info
   */
  public function getOptions(Command $command, AnnotationData $annotationData) {
    $annotations = $annotationData->getArrayCopy();
    $annotations['field-labels'] .= "\n" . implode("\n", array(
      'mysql_replica_command: MySQL Replica Command',
      'mysql_replica_username: MySQL Replica Username',
      'mysql_replica_host: MySQL Replica Host',
      'mysql_replica_password: MySQL Replica Password',
      'mysql_replica_url: MySQL Replica URL',
      'mysql_replica_port: MySQL Replica Port',
      'mysql_replica_database: MySQL Replica Database',
    ));
    $annotationData->exchangeArray($annotations);
  }

  /**
   * Appends MySQL replica data to connection info where applicable.
   *
   * @param PropertyList $result
   * @param CommandData $commandData
   *
   * @hook alter connection:info
   */
  public function process($result, CommandData $commandData) {
    $results = $result->getArrayCopy();
    $siteEnv = $commandData->arguments()['site_env'];
    list(, $env) = $this->getSiteEnv($siteEnv);

    $dbservers = array();
    foreach ($env->getBindings()->all() as $binding) {
      if ($binding->get('environment') === $env->id
          && $binding->get('type') === 'dbserver'
          && $binding->get('slave_of')) {
        $dbservers[] = $binding;
      }
    }

    if (!empty($dbservers)) {
      $replica_info = $this->interpretBinding(array_shift($dbservers));
      foreach ($replica_info as $field => $value) {
        $results['mysql_replica_' . $field] = $value;
      }
    }

    $result->exchangeArray($results);
    return $result;
  }

  /**
   * Returns an array of connection details, given a dbserver binding.
   *
   * @param Binding $dbServer
   * @return array
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
