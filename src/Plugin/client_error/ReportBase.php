<?php

namespace Drupal\client_error_trace\Plugin\client_error;

use Drupal\client_error_trace\Translatable;
use Guzzle\Http\Url;

/**
 * Base class for all ClientError reports.
 *
 * @class ReportBase
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
abstract class ReportBase implements ReportInterface {
  use Translatable;

  /**
   * The result of this report, as a constant from ReportInterface.
   *
   * @var int
   */
  protected $result;

  /**
   * The Drupal account associated with this report.
   *
   * @var \stdClass
   */
  protected $account;

  /**
   * Construct a new report for a given URL and result.
   *
   * @param \Guzzle\Http\Url $url
   *   The URL that was tested.
   * @param mixed $account
   *   The Drupal account associated with this report.
   * @param int $result
   *   The result of the ClientError test, as a constant from ReportInterface.
   */
  public function __construct(Url $url, $account, $result) {
    $this->account = $account;
    $this->setResult($result);
  }

  /**
   * {@inheritdoc}
   */
  public function result() {
    return $this->result;
  }

  /**
   * {@inheritdoc}
   */
  public function hasSuggestions() {
    $suggestions = $this->suggestions();
    return !empty($suggestions);
  }

  /**
   * Set the result of this report.
   *
   * @param int $result
   *   A constant from ReportInterface.
   */
  protected function setResult($result) {
    $statuses = array(
      static::FAILED,
      static::SUCCESS,
      static::SKIPPED,
    );

    if (!in_array($result, $statuses)) {
      throw new \InvalidArgumentException('$result is not a valid status.');
    }

    $this->result = $result;
  }
}
