<?php

/**
 * Provides basic functionality for looking up and creating PersonFieldTypes in Stitch Labs.
 */

class PersonFieldType {

  const PERSONFIELDTYPE_API_BASE_URL = 'api2/';
  const PERSONFIELDTYPE_STITCH_CLASS_NAME = 'PersonFieldTypes';

  // Contains the Stitch instance for this PersonFieldType.
  public $stitch;

  // // PersonFieldType fields
  // public $name;
  // public $field_type;
  // public $id;

  /**
   * Constructor, set the stitch object so that this object can interact
   * with Stitch.
   */
  public function __construct($stitch) {
    $this->stitch = $stitch;

  }

  /**
   * Wrapper function to look up PersonFieldTypes by their ids.
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
   * Wrapper function to look up PersonFieldTypes by their ids.
   */
  public function lookUpId($ids = array(), $detail = TRUE) {
    $args = array(
      $this::PERSONFIELDTYPE_STITCH_CLASS_NAME => array($ids),
    );
    return $this->lookUp($args, $detail);
  }

  /**
   * Utility function to lookup PersonFieldTypes by arguments provided.
   */
  public function lookUp($arguments = array(), $detail = FALSE) {
    $action = 'read';
    $arguments['action'] = $action;
    return $this->request($action, $arguments, $detail);
  }

  /**
   * This is a wrapper function for making requests to Stitch.
   */
  private function request($action, $arguments = array(), $detail = FALSE) {
    $stitch = $this->stitch;
    $url = $stitch::STITCH_API_BASE_URL . $this::PERSONFIELDTYPE_API_BASE_URL;
    $url .= $this->getAPIVersion($action) . '/' . $this::PERSONFIELDTYPE_STITCH_CLASS_NAME;

    // The detail flag at the end increases specificity. However it requires that
    // a stitch id be provided and limits results to 1. Only for read actions.
    if ($detail && $action == 'read') {
      $url .= '/detail';
    }

    return $stitch->curlRequest($url, $arguments);
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