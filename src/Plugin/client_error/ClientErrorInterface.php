<?php


namespace Drupal\client_error_trace\Plugin\client_error;

use Guzzle\Http\Url;

/**
 * Interface for all ClientError plugins.
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
interface ClientErrorInterface {

  /**
   * Execute a test for a given URL.
   *
   * @param \Guzzle\Http\Url $url
   *   The URL to test for client errors.
   * @param mixed $account
   *   (optional) The Drupal account to use. Defaults to the anonymous user.
   *
   * @return \Drupal\client_error_trace\Plugin\client_error\ReportInterface
   *   A report with the results from the ClientError test.
   */
  public function execute(Url $url, $account = NULL);
}
