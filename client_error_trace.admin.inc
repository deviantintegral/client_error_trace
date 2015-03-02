<?php
/**
 * @file
 * Administration page callbacks.
 */

use Drupal\client_error_trace\ClientErrorPluginManager;
use GuzzleHttp\Url;

/**
 * Form callback to run tests against a URL.
 */
function client_error_trace_form($form, &$form_state) {
  $form = array();

  _client_error_trace_report($form, $form_state);

  $form['url'] = array(
    '#type' => 'textfield',
    '#title' => t('The public URL to trace'),
    '#description' => t('Enter the URL to test as used by the public.'),
    '#required' => TRUE,
    '#default_value' => isset($form_state['values']['url']) ? $form_state['values']['url'] : '',
  );

  _client_error_trace_plugins($form);

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Trace errors'),
  );

  return $form;
}

/**
 * Helper function to add a list of available validations to a form.
 *
 * @param array &$form
 *   The form to add a 'plugins' item to.
 */
function _client_error_trace_plugins(array &$form) {
  $manager = ClientErrorPluginManager::create();
  $options = array();
  foreach ($manager->getDefinitions() as $plugin) {
    $options[$plugin['id']] = $plugin['description'];
  }

  $form['plugins'] = array(
    '#type' => 'checkboxes',
    '#options' => $options,
    '#title' => t('Validations'),
    '#default_value' => array_keys($options),
  );
}

/**
 * Helper function to add a client error report to a form.
 *
 * @param array &$form
 *   The form to modify.
 * @param array &$form_state
 *   The current state of the form.
 */
function _client_error_trace_report(array &$form, array &$form_state) {
  if (isset($form_state['report'])) {
    $form['report'] = array(
      '#markup' => $form_state['report'],
    );

    $form['html_report'] = array(
      '#title' => t('Report in HTML format'),
      '#type' => 'fieldset',
      '#collapsed' => TRUE,
      '#collapsible' => TRUE,
      'html' => array(
        '#type' => 'textarea',
        '#description' => t('Use this for easy pasting into a support ticket.'),
        '#value' => $form_state['report'],
        '#attributes' => array('readonly' => 'readonly'),
      ),
    );
  }
}

/**
 * Form callback to run tests against a URL.
 *
 * @param array &$form
 *   The form being submitted.
 * @param array &$form_state
 *   The current state of the form.
 *
 * @throws \Exception
 *   Thrown if a client_error plugin could not execute.
 */
function client_error_trace_form_submit(array &$form, array &$form_state) {
  $manager = ClientErrorPluginManager::create();
  $url = Url::fromString($form_state['values']['url']);
  $results = $manager->execute($url, $form_state['values']['plugins']);

  $form_state['report'] = theme('client_error_trace_report', array('url' => $url, 'results' => $results));

  $form_state['rebuild'] = TRUE;
}
