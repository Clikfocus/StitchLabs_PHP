<?php

/**
 * Provides basic functionality for looking up and creating people in Stitch Labs.
 */

class InvoicePayment {

  const INVOICEPAYMENT_API_BASE_URL = 'api2/';
  const INVOICEPAYMENT_STITCH_CLASS_NAME = 'InvoicePayments';

  public $stitch;

  public $id;
  public $amount;
  public $fee;
  public $notes;
  public $links;
  public $payment_date;

  /**
   * Create a new instance of the InvoicePayment Object.
   */
  public function __construct($stitch) {
    $this->stitch = $stitch;
    $this->links = new stdClass();
  }

  /**
   * Wrapper function to look up InvoicePayments by their ids.
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
   * Wrapper function to look up InvoicePayments by their ids.
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
      $this::INVOICEPAYMENT_STITCH_CLASS_NAME => array($ids),
    );
    return $this->lookUp($args, $detail);
  }

  /**
   * Utility function to lookup invoice payments by arguments provided.
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
    $this->id = $return->InvoicePayments[0]->id;
    return $return;
  }

  /**
   * This is a wrapper function for making requests to Stitch.
   */
  private function request($action, $arguments = array(), $detail = FALSE) {
    $stitch = $this->stitch;
    $url = $stitch::STITCH_API_BASE_URL . $this::INVOICEPAYMENT_API_BASE_URL;
    $url .= $this->getAPIVersion($action) . '/' . $this::INVOICEPAYMENT_STITCH_CLASS_NAME;

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
    $invoice_payment_values = clone $this;
    unset($invoice_payment_values->stitch);

    if (empty($invoice_payment_values->id)) {
      unset($invoice_payment_values->id);
    }

    $args = array(
      'action' => $action,
      $this::INVOICEPAYMENT_STITCH_CLASS_NAME => array(
        $invoice_payment_values,
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

}
