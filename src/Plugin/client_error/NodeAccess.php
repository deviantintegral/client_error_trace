<?php
/**
 * @file
 * ClientError plugin to validate node access.
 */

namespace Drupal\client_error_trace\Plugin\client_error;

use Drupal\client_error_trace\Annotation\ClientError;
use GuzzleHttp\Url;

/**
 * ClientError plugin to validate node access.
 *
 * @ClientError(
 *   id="node_access",
 *   description="Validate that a user is granted access by the node access subsystem.",
 *   statusCode=403
 * )
 *
 * @class NodeAccess
 *
 * @package Drupal\client_error_trace\Plugin\client_error
 */
class NodeAccess extends ClientErrorBase {

  /**
   * {@inheritdoc}
   */
  public function execute(Url $url, $account = NULL) {
    $account = $this->defaultAccount($account);
    $node = NULL;
    $e = NULL;

    if ($this->urlIsNode($url) && $node = node_load($this->urlNodeId($url))) {
      try {
        $this->access('view', $node, $account);
        $result = ReportInterface::SUCCESS;
      }
      catch (NodeAccessException $e) {
        $result = ReportInterface::FAILED;
      }
    }
    else {
      $result = ReportInterface::SKIPPED;
    }

    return new NodeAccessReport($url, $account, $result, $node, $e);
  }

  /**
   * Clone of node_access() that throws exceptions when denying access.
   *
   * @param string $op
   *   The operation on the node, such as 'view' or 'edit'.
   * @param mixed $node
   *   The node to check access for.
   * @param mixed $account
   *   (optional) The Drupal account to check access for. Defaults to the
   *   current user.
   *
   * @throws NodeAccessException
   *   Thrown when access is denied to the node.
   *
   * @see node_access()
   *
   * @return bool
   *   TRUE if access is granted to the node.
   */
  protected function access($op, $node, $account = NULL) {
    $rights = &drupal_static(__FUNCTION__, array());

    if (!$node || !in_array($op, array('view', 'update', 'delete', 'create'), TRUE)) {
      // If there was no node to check against, or the $op was not one of the
      // supported ones, we return access denied.
      throw new NodeAccessException('$node was not defined or $op is not supported.');
    }
    // If no user object is supplied, the access check is for the current user.
    if (empty($account)) {
      $account = $GLOBALS['user'];
    }

    // $node may be either an object or a node type. Since node types cannot be
    // an integer, use either nid or type as the static cache id.

    $cid = is_object($node) ? $node->nid : $node;

    // If we've already checked access for this node, user and op, return from
    // cache.
    if (isset($rights[$account->uid][$cid][$op])) {
      return $rights[$account->uid][$cid][$op];
    }

    if (user_access('bypass node access', $account)) {
      $rights[$account->uid][$cid][$op] = TRUE;
      return TRUE;
    }
    if (!user_access('access content', $account)) {
      $rights[$account->uid][$cid][$op] = FALSE;
      throw new NodeAccessException('User does not have the access content permission.');
    }

    // We grant access to the node if both of the following conditions are met:
    // - No modules say to deny access.
    // - At least one module says to grant access.
    // If no module specified either allow or deny, we fall back to the
    // node_access table.
    // This hook has been modified to throw an exception on a DENY.
    $access = $this->moduleInvokeAll('node_access', $node, $op, $account);
    if (in_array(NODE_ACCESS_DENY, $access, TRUE)) {
      $rights[$account->uid][$cid][$op] = FALSE;
      return FALSE;
    }
    elseif (in_array(NODE_ACCESS_ALLOW, $access, TRUE)) {
      $rights[$account->uid][$cid][$op] = TRUE;
      return TRUE;
    }

    // Check if authors can view their own unpublished nodes.
    if ($op == 'view' && !$node->status && user_access('view own unpublished content', $account) && $account->uid == $node->uid && $account->uid != 0) {
      $rights[$account->uid][$cid][$op] = TRUE;
      return TRUE;
    }

    // If the module did not override the access rights, use those set in the
    // node_access table.
    if ($op != 'create' && $node->nid) {
      if (module_implements('node_grants')) {
        $query = db_select('node_access');
        $query->addExpression('1');
        $query->condition('grant_' . $op, 1, '>=');
        $nids = db_or()->condition('nid', $node->nid);
        if ($node->status) {
          $nids->condition('nid', 0);
        }
        $query->condition($nids);
        $query->range(0, 1);

        $grants = db_or();
        foreach (node_access_grants($op, $account) as $realm => $gids) {
          foreach ($gids as $gid) {
            $grants->condition(db_and()
              ->condition('gid', $gid)
              ->condition('realm', $realm)
            );
          }
        }
        if (count($grants) > 0) {
          $query->condition($grants);
        }
        $result =  (bool) $query
          ->execute()
          ->fetchField();
        $rights[$account->uid][$cid][$op] = $result;

        if (!$result) {
          throw new NodeAccessException(format_string('The {node_access} table denied access to node ID !node with query !query.', array('!node' => $node->nid, '!query' => $this->printQuery($query))));
        }
        return $result;
      }
      elseif (is_object($node) && $op == 'view' && $node->status) {
        // If no modules implement hook_node_grants(), the default behavior is to
        // allow all users to view published nodes, so reflect that here.
        $rights[$account->uid][$cid][$op] = TRUE;
        return TRUE;
      }
    }

    throw new NodeAccessException('Default return FALSE was hit in node_access().');
  }

  /**
   * Clone of module_invoke_all() that throws an exception on a FALSE return.
   *
   * @param string $hook
   *   The hook to invoke.
   *
   * @see module_invoke_all()
   *
   * @throws \Drupal\client_error_trace\Plugin\client_error\NodeAccessException
   *   Thrown if a node access hook denies access.
   *
   * @return array
   *   An array of node access returns.
   */
  protected function moduleInvokeAll($hook) {
    $args = func_get_args();
    // Remove $hook from the arguments.
    unset($args[0]);
    $return = array();
    foreach (module_implements($hook) as $module) {
      $function = $module . '_' . $hook;
      if (function_exists($function)) {
        $result = call_user_func_array($function, $args);
        $this->validateHookNodeAccess($result, $function, $args);
        if (isset($result) && is_array($result)) {
          $return = array_merge_recursive($return, $result);
        }
        elseif (isset($result)) {
          $return[] = $result;
        }
      }
    }

    return $return;
  }

  /**
   * Validate that a node access hook does not deny access.
   *
   * @param int $result
   *   The result of the node access hook.
   * @param string $function
   *   The hook that is being validated.
   * @param array $args
   *   An array of arguments passed to hook_node_access().
   *
   * @throws \Drupal\client_error_trace\Plugin\client_error\NodeAccessException
   *   Thrown if $result is NODE_ACCESS_DENY.
   */
  protected function validateHookNodeAccess($result, $function, array $args) {
    if ($result == NODE_ACCESS_DENY) {
      throw new NodeAccessException(format_string('!function denied !op access to node ID !node for !account.', array(
        '!function' => $function,
        '!op' => $args[1],
        '!node' => $args[0],
        '!account' => $args[3],
      )));
    }
  }

  /**
   * Helper function to return a SQL query string with placeholders.
   *
   * @param \SelectQuery $query
   *   The query to convert to a string.
   *
   * @see dpq()
   *
   * @return string
   *   The SQL query that would be executed by $query.
   */
  protected function printQuery(\SelectQuery $query) {
    if (method_exists($query, 'preExecute')) {
      $query->preExecute();
    }
    $sql = (string) $query;
    $quoted = array();
    $connection = \Database::getConnection();
    foreach ((array) $query->arguments() as $key => $val) {
      $quoted[$key] = $connection->quote($val);
    }
    $sql = strtr($sql, $quoted);
    return $sql;
  }

}
