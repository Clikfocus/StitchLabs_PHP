<?php

/**
 * Provides basic functionality for looking up and creating variants in Stitch Labs.
 */

class Variant {

  const VARIANT_API_BASE_URL = 'api2/';
  const VARIANT_STITCH_CLASS_NAME = 'Variants';

  // Contains the Stitch instance for this Variant.
  public $stitch;

  /**
   * Constructor, set the stitch object so that this object can interact
   * with Stitch.
   */
  public function __construct($stitch) {
    $this->stitch = $stitch;
    //$this->name = $name;

  }

  /**
   * Wrapper function to look up variants by their ids.
   *
   * @param: $filters
   * Expects an array of conditionals ("and", "or") containing arrays of
   * field => value mappings. Each mapping may also specify an operator
   * in the format of "operation": [operator].
   */
  public function lookUpFiltered($filters = array(), $page_size = FALSE, $page_number = 1) {
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
   * Wrapper function to look up variants by their ids.
   */
  public function lookUpId($ids = array(), $detail = TRUE) {
    $args = array(
      $this::VARIANT_STITCH_CLASS_NAME => array($ids),
    );
    return $this->lookUp($args, $detail);
  }

  /**
   * Utility function to lookup variants by arguments provided.
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
    $url = $stitch::STITCH_API_BASE_URL . $this::VARIANT_API_BASE_URL;
    $url .= $this->getAPIVersion($action) . '/' . $this::VARIANT_STITCH_CLASS_NAME;

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
    $variant_values = clone $this;
    unset($variant_values->stitch);

    $args = array(
      'action' => $action,
      $this::VARIANT_STITCH_CLASS_NAME => array(
        $variant_values,
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
