<?php


namespace Drupal\client_error_trace\Plugin\client_error;

use GuzzleHttp\Url;

/**
 * Interface for all ClientError plugins.
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
interface ClientErrorInterface {

  /**
   * Execute a test for a given URL.
   *
   * @param \GuzzleHttp\Url $url
   *   The URL to test for client errors.
   *
   * @return ReportInterface
   *   A report with the results from the ClientError test.
   */
  public function execute(Url $url);
}
