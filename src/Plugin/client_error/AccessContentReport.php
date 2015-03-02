<?php

namespace Drupal\client_error_trace\Plugin\client_error;

/**
 * Report the results of an AccessContent check.
 *
 * @see AccessContent
 *
 * @class AccessContentReport
 * @package Drupal\client_error_trace\Plugin\client_error
 */
class AccessContentReport extends ReportBase {

  /**
   * {@inheritdoc}
   */
  public function resultMessage() {
    switch ($this->result) {
      case static::SUCCESS:
        return t("Anonymous users have the 'access content' permission.");

      case static::FAILED:
        return t("Anonymous users do not have the 'access content' permission.");

      case static::SKIPPED:
      default:
        return t("Access content was skipped as it does not appear to affect this request.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function suggestions() {
    switch ($this->result) {
      case static::FAILED:
        return array(
          t('Give anonymous users <a href="@permissions">permission to access content</a>.', array('@permissions' => url('admin/people/permissions'))),
        );

      case static::SKIPPED:
        return array(
          t("Validate the URL is for a node or entity type that depends on the 'access content' permission."),
        );
    }

    return array(
      t('There are no suggestions available.'),
    );
  }
}
