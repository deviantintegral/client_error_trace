<?php
/**
 * @file
 * Report the results of an NodeAccess check.
 */

namespace Drupal\client_error_trace\Plugin\client_error;
use Guzzle\Http\Url;

/**
 * Report the results of an NodeAccess check.
 *
 * @see NodeAccess
 *
 * @class NodeAccessReport
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
class NodeAccessReport extends ReportBase {

  /**
   * The node that access was checked against.
   *
   * @var \stdClass
   */
  protected $node;

  /**
   * The exception thrown if node_access() failed.
   *
   * @var NodeAccessException
   */
  protected $nodeAccessException;

  /**
   * Construct a new NodeAccessReport.
   *
   * @param \Guzzle\Http\Url $url
   *   The URL that was checked for access.
   * @param mixed $account
   *   The account that was used when checking $url.
   * @param int $result
   *   The result from the client error check, as a constant from
   *   ReportInterface.
   * @param mixed $node
   *   (optional) The node that was checked.
   * @param NodeAccessException $e
   *   (optional) The exception that was thrown.
   */
  public function __construct(Url $url, $account, $result, $node = NULL, NodeAccessException $e = NULL) {
    if ($result != ReportInterface::SKIPPED && !$node) {
      throw new \InvalidArgumentException('Node access reports must include a node unless the report has been skipped.');
    }

    $this->node = $node;
    $this->nodeAccessException = $e;
    parent::__construct($url, $account, $result);
  }

  /**
   * {@inheritdoc}
   */
  public function resultMessage() {
    switch ($this->result) {
      case static::SUCCESS:
        if ($this->account->uid) {
          return $this->t("%user has view access to node ID @nid.", array('%user' => $this->account->name, '@nid' => $this->node->nid));
        }
        return $this->t("Anonymous users have view access to node ID @nid.", array('@nid' => $this->node->nid));

      case static::FAILED:
        if ($this->account->uid) {
          return $this->t("%user does not have view access to node ID @nid.", array('%user' => $this->account->name, '@nid' => $this->node->nid));
        }
        return $this->t("Anonymous users do not have view access to node ID @nid.", array('@nid' => $this->node->nid));

      case static::SKIPPED:
      default:
        return $this->t("Node access was skipped as the URL does not appear to map to a node.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function suggestions() {
    $suggestions = array();

    switch ($this->result) {
      case static::FAILED:
        $suggestions[] = $this->t('Check node access grants.');
        if ($this->nodeAccessException) {
          $suggestions[] = $this->t('!message', array('!message' => $this->nodeAccessException->getMessage()));
        }
        break;

      case static::SKIPPED:
        $suggestions[] = $this->t("Validate the URL is for a node.");
        break;
    }

    return $suggestions;
  }
}
