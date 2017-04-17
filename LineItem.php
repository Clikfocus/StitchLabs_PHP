<?php

/**
 * Provides basic functionality for looking up and creating line items in Stitch Labs.
 */

class LineItem {

 // const LINE_ITEM_API_BASE_URL = 'api2/';
  const LINE_ITEM_STITCH_CLASS_NAME = 'SalesOrderLineItems';

  // Contains the Stitch instance for this Line Item.
  //public $stitch;

  public $created_at;
  public $description;
  public $links = array(
    'Variants' => array(),
  );
  public $price;
  public $quantity;

  /**
   * Create line item with values from a Variant.
   */
  public function __construct($variant_id, $description = '') {
    $this->links['Variants'][] = array('id' => $variant_id);
    $this->description = $description;

  }

  /**
   * Wrapper function to look up line_items by their ids.
   *
   * @param: $filters
   * Expects an array of conditionals ("and", "or") containing arrays of
   * field => value mappings. Each mapping may also specify an operator
   * in the format of "operation": [operator].
   */
  // public function lookUpFiltered($filters = array()) {
  //   $args = array(
  //     'filter' => $filters,
  //   );
  //   return $this->lookUp($args);
  // }

  /**
   * Wrapper function to look up line_items by their ids.
   */
  // public function lookUpId($ids = array(), $detail = TRUE) {
  //   $args = array(
  //     $this::LINE_ITEM_STITCH_CLASS_NAME => array($ids),
  //   );
  //   return $this->lookUp($args, $detail);
  // }

  /**
   * Utility function to lookup line_items by arguments provided.
   */
  // public function lookUp($arguments = array(), $detail = FALSE) {
  //   $action = 'read';
  //   $arguments['action'] = $action;
  //   return $this->request($action, $arguments, $detail);
  // }

}