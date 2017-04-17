<?php

/**
 * Provides basic functionality for looking up and creating PurchaseOrders in Stitch Labs.
 */

class PurchaseOrder {

  const PURCHASE_ORDER_API_BASE_URL = 'api2/';
  const PURCHASE_ORDER_STITCH_CLASS_NAME = 'PurchaseOrders';

  // Contains the Stitch instance for this PurchaseOrder.
  public $stitch;

  /**
   * Constructor, set the stitch object so that this object can interact
   * with Stitch.
   */
  public function __construct($stitch) {
    $this->stitch = $stitch;

  }

  /**
   * Wrapper function to look up PurchaseOrders by their ids.
   *
   * @param: $filters
   * Expects an array of conditionals ("and", "or") containing arrays of
   * field => value mappings. Each mapping may also specify an operator
   * in the format of "operation": [operator].
   */
  public function lookUpFiltered($filters = array(), $page_size = FALSE) {
    $args = array(
      'filter' => $filters,
    );

    // Allow the number of results to be specified.
    if (!empty($page_size)) {
      $args['page_size'] = $page_size;
    }

    return $this->lookUp($args);
  }

  /**
   * Wrapper function to look up PurchaseOrders by their ids.
   */
  public function lookUpId($ids = array(), $detail = TRUE) {
    // If $ids is empty, check to see if this contact has an id.
    if (empty($ids)) {
      if (!empty($this->id)) {
        $ids = $this->id;
      }
    }

    if (!empty($ids)) {
      // If $ids isn't an array, then assume it is a single id.
      if (!is_array($ids)) {
        $ids = array('id' => $ids);
      }
      // Since there is only one, go ahead and get all of the details that we can.
      if (count($ids) == 1) {
        $detail = TRUE;
      }
    }

    $args = array(
      $this::PURCHASE_ORDER_STITCH_CLASS_NAME => array($ids),
    );
    return $this->lookUp($args, $detail);
  }

  /**
   * Utility function to lookup PurchaseOrders by arguments provided.
   */
  public function lookUp($arguments = array(), $detail = FALSE) {
    $action = 'read';
    $arguments['action'] = $action;
    return $this->request($action, $arguments, $detail);
  }

  /**
   * Utility function to save this object.
   */
  public function save() {
    $action = 'write';
    $arguments = $this->getArguments($action);
    $this->request($action, $arguments);
  }

  /**
   * This is a wrapper function for making requests to Stitch.
   */
  private function request($action, $arguments = array(), $detail = FALSE) {
    $stitch = $this->stitch;
    $url = $stitch::STITCH_API_BASE_URL . $this::PURCHASE_ORDER_API_BASE_URL;
    $url .= $this->getAPIVersion($action) . '/' . $this::PURCHASE_ORDER_STITCH_CLASS_NAME;

    // The detail flag at the end increases specificity. However it requires that
    // a stitch id be provided and limits results to 1. Only for read actions.
    if ($detail && $action == 'read') {
      $url .= '/detail';
    }

    return $stitch->curlRequest($url, $arguments);
  }

  /**
   * Build out arguments based on the object values for this object.
   */
  public function getArguments($action) {
    // Copy the values of this object so that the Stitch object can be exluded
    // from the arguments list.
    $purchase_order_values = clone $this;
    unset($purchase_order_values->stitch);

    $args = array(
      'action' => $action,
      $this::PURCHASE_ORDER_STITCH_CLASS_NAME => array(
        $purchase_order_values,
      ),
    );

    return $args;
  }

  /**
   * Retrieve the API version for the specified action.
   */
  public function getAPIVersion($action) {
    switch ($action) {
      case 'write':
        return 'v1';
      case 'read':
        return 'v2';
    }
  }

}