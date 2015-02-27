<?php

namespace Drupal\client_error_trace\Plugin\client_error;

/**
 * Interface for all ClientError reports.
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
interface ReportInterface {

  /**
   * The ClientError test failed.
   *
   * @var int
   */
  const FAILED = 0;

  /**
   * The ClientError test passed.
   *
   * @var int
   */
  const SUCCESS = 1;

  /**
   * The ClientError test was skipped.
   *
   * @var int
   */
  const SKIPPED = 2;

  /**
   * Return the result of this report.
   *
   * @return int
   *   A constant from ReportInterface.
   */
  public function result();

  /**
   * Return the message associated with this report.
   *
   * @return string
   */
  public function resultMessage();

  /**
   * Return if this report has suggestions.
   *
   * @return bool
   *   TRUE if this report has suggestions to offer, FALSE otherwise.
   */
  public function hasSuggestions();

  /**
   * Return an array of suggestions offered by report to resolve the failure.
   *
   * @return array
   *   An array of strings with human-readable suggestions.
   */
  public function suggestions();
}
