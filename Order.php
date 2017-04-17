<?php

/**
 * Provides basic functionality for looking up and creating orders in Stitch Labs.
 */

class Order {

 const ORDER_API_BASE_URL = 'api2/';
  const ORDER_STITCH_CLASS_NAME = 'SalesOrders';

  // Contains the Stitch instance for this order.
  public $stitch;

  // Order fields
  public $created_at;
  public $currency_iso = 'USD';
  public $deleted;
  public $discount;
  public $id;
  public $links;
  public $local_id;
  public $notes;
  public $order_date;
  public $po_num;
  public $s_and_h;
  public $ship_date;
  public $ship_method_complete;
  public $ship_method_id;
  public $sku_subtotal;
  public $status_deliver;
  public $status_invoice;
  public $status_package;
  public $status_packing_slip;
  public $status_return;
  public $tax_percent;
  public $tax_total;
  public $total;
  public $total_paid_balance;
  public $updated_at;

  /**
   * Constructor, set the stitch object so that this object can interact
   * with Stitch.
   */
  public function __construct($stitch) {
    $this->stitch = $stitch;
    $this->order_date = date('c');

  }

  /**
   * Wrapper function to look up variants by their ids.
   *
   * @param: $filters
   * Expects an array of conditionals ("and", "or") containing arrays of
   * field => value mappings. Each mapping may also specify an operator
   * in the format of "operation": [operator].
   */
  public function lookUpFiltered($filters = array()) {
    $args = array(
      'filter' => $filters,
    );
    return $this->lookUp($args);
  }

  /**
   * Wrapper function to look up orders by their ids.
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
        $detail = TRUE;
      }
      // Since there is only one, go ahead and get all of the details that we can.
      if (count($ids) == 1) {
        $detail = TRUE;
      }
    }

    $args = array(
      $this::ORDER_STITCH_CLASS_NAME => array($ids),
    );

    return $this->lookUp($args, $detail);
  }

  /**
   * Utility function to lookup orders by arguments provided.
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
    $return = $this->request($action, $arguments);
    $this->id = $return->SalesOrders[0]->id;
    return $return;
  }

  /**
   * This is a wrapper function for making requests to Stitch.
   */
  private function request($action, $arguments = array(), $detail = FALSE) {
    $stitch = $this->stitch;
    $url = $stitch::STITCH_API_BASE_URL . $this::ORDER_API_BASE_URL;
    $url .= $this->getAPIVersion($action) . '/' . $this::ORDER_STITCH_CLASS_NAME;

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
  private function getArguments($action) {
    $order_values = clone $this;
    unset($order_values->stitch);
    if (empty($order_values->id)) {
      unset($order_values->id);
    }
    $args = array(
      'action' => $action,
      $this::ORDER_STITCH_CLASS_NAME => array(
        $order_values,
      ),
    );

    return $args;
  }

  /**
   * Retrieve the API version for the specified action.
   */
  private function getAPIVersion($action) {
    switch ($action) {
      case 'write':
        return 'v1';
      case 'read':
        return 'v2';
    }
  }

  /**
   * Utility function to take all of the values from a given object and
   * assign them to this one. This is used for assigning values of search
   * results to the original object.
   */
  public function setValues($object) {
    foreach($this as $property => &$value) {
      if (!empty($object->{$property})) {
        $value = $object->{$property};
      }
    }
  }

  /**
   * Utility function to add the given line item to the order.
   */
  public function addLineItem($line_item) {
    $this->links->LineItems[] = $line_item;
  }

    /**
   * Utility function to add the given contact to the order.
   */
  public function addContact($contact) {
    if (!empty($contact->id)) {
      $this->links->Contacts[] = array('id' =>$contact->id);
    }
  }

}