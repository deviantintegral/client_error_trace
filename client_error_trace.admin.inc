<?php
/**
 * @file
 * Administration page callbacks.
 */

use Drupal\client_error_trace\ClientErrorPluginManager;
use Drupal\client_error_trace\Plugin\client_error\ClientErrorInterface;
use GuzzleHttp\Url;

/**
 * Form callback to run tests against a URL.
 */
function client_error_trace_form($form, &$form_state) {
  $form = array();

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

  $form['url'] = array(
    '#type' => 'textfield',
    '#title' => t('The public URL to trace'),
    '#description' => t('Enter the URL to test as used by the public.'),
    '#required' => TRUE,
    '#default_value' => isset($form_state['values']['url']) ? $form_state['values']['url'] : '',
  );

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

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Trace errors'),
  );

  return $form;
}

/**
 * Form callback to run tests against a URL.
 */
function client_error_trace_form_submit(&$form, &$form_state) {
  $url = Url::fromString($form_state['values']['url']);
  $manager = ClientErrorPluginManager::create();
  $results = array();
  foreach ($form_state['values']['plugins'] as $plugin) {
    /** @var ClientErrorInterface $instance */
    $instance = $manager->createInstance($plugin);
    $report = $instance->execute($url);

    $results[] = theme('client_error_trace_item', array(
      'result' => $report->result(),
      'description' => check_plain($manager->getDefinition($plugin)['description']),
      'message' => $report->resultMessage(),
      'suggestions' => $report->suggestions(),
    ));

  }

  $form_state['report'] = theme('client_error_trace_report', array('url' => $url, 'results' => $results));

  $form_state['rebuild'] = TRUE;
}
