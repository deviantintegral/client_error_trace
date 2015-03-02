<?php

namespace Drupal\client_error_trace;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\plug\Util\Module;

/**
 * Manager to load ClientError plugins.
 *
 * Use this class to find all available ClientError plugins, read their
 * annotations, and load instances of them.
 *
 * @class ClientErrorPluginManager
 * @package Drupal\client_error_trace
 */
class ClientErrorPluginManager extends DefaultPluginManager {

  /**
   * Helper function to create a new instance loaded with all module plugins.
   * @return static
   */
  public static function create() {
    return new static(Module::getNamespaces());
  }

  /**
   * @param \Traversable $namespaces
   */
  public function __construct(\Traversable $namespaces) {
    parent::__construct('Plugin/client_error', $namespaces, 'Drupal\client_error_trace\Plugin\client_error\ClientErrorInterface', '\Drupal\client_error_trace\Annotation\ClientError');
    // TODO: $this->setCacheBackend($cache)
    $this->alterInfo('client_error_plugin');
  }
}
