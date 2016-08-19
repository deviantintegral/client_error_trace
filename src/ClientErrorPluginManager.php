<?php
/**
 * @file
 * Manager to load ClientError plugins.
 */

namespace Drupal\client_error_trace;

use Drupal\client_error_trace\Plugin\client_error\ClientErrorInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\plug\Util\Module;
use Guzzle\Http\Url;

/**
 * Manager to load ClientError plugins.
 *
 * Use this class to find all available ClientError plugins, read their
 * annotations, and load instances of them.
 *
 * @class ClientErrorPluginManager
 *
 * @package Drupal\client_error_trace
 */
class ClientErrorPluginManager extends DefaultPluginManager {

  /**
   * Helper function to create a new instance loaded with all module plugins.
   *
   * @return static
   *   A new instance of this manager.
   */
  public static function create() {
    return new static(Module::getNamespaces());
  }

  /**
   * Construct a new ClientErrorPluginManager.
   *
   * @param \Traversable $namespaces
   *   An array of namespaces to search for client_error plugins in.
   */
  public function __construct(\Traversable $namespaces) {
    parent::__construct('Plugin/client_error', $namespaces, 'Drupal\client_error_trace\Plugin\client_error\ClientErrorInterface', '\Drupal\client_error_trace\Annotation\ClientError');
    // TODO: $this->setCacheBackend($cache)
    $this->alterInfo('client_error_plugin');
  }

  /**
   * Helper method to execute ClientError plugins against a URL.
   *
   * @param Url $url
   *   The URL to execute plugins against.
   * @param array $plugins
   *   An array of plugin identifiers.
   *
   * @throws \Exception
   *   Thrown if a plugin could not be instantiated.
   *
   * @return array
   *   An array of report results as HTML strings.
   */
  public function execute(Url $url, array $plugins) {
    $results = array();
    foreach ($plugins as $plugin) {
      /** @var ClientErrorInterface $instance */
      $instance = $this->createInstance($plugin);
      $report = $instance->execute($url);

      $results[] = theme('client_error_trace_item', array(
        'result' => $report->result(),
        'description' => check_plain($this->getDefinition($plugin)['description']),
        'message' => $report->resultMessage(),
        'suggestions' => $report->suggestions(),
      ));
    }

    return $results;
  }

}
