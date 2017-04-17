<?php

/**
 * Provides basic functionality for looking up and creating contacts in Stitch Labs.
 */

class Contact {

  const CONTACT_API_BASE_URL = 'api2/';
  const CONTACT_STITCH_CLASS_NAME = 'Contacts';

  // Contains the Stitch instance for this contact.
  public $stitch;

  // Contact fields
  public $archived;
  public $created_at;
  public $deleted;
  public $id;
  // Object Reference Fields
  public $links;
  public $local_id;
  public $name;
  public $nature;
  public $notes;
  public $reseller;
  public $taxid;
  public $website;

  /**
   * Constructor, set the stitch object so that this object can interact
   * with Stitch.
   */
  public function __construct($stitch, $name = NULL) {
    $this->stitch = $stitch;
    $this->name = $name;
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
   * Wrapper function to look up contacts by their ids.
   */
  public function lookUpId($ids = array(), $detail = FALSE) {
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
      $this::CONTACT_STITCH_CLASS_NAME => array($ids),
    );
    return $this->lookUp($args, $detail);
  }

  /**
   * Utility function to lookup contacts by arguments provided.
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
    $this->id = $return->Contacts[0]->id;
    return $return;
  }

  /**
   * This is a wrapper function for making requests to Stitch.
   */
  private function request($action, $arguments = array(), $detail = FALSE) {
    $stitch = $this->stitch;
    $url = $stitch::STITCH_API_BASE_URL . $this::CONTACT_API_BASE_URL;
    $url .= $this->getAPIVersion($action) . '/' . $this::CONTACT_STITCH_CLASS_NAME;

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
    $contact_values = clone $this;
    unset($contact_values->stitch);
    $args = array(
      'action' => $action,
      $this::CONTACT_STITCH_CLASS_NAME => array(
        $contact_values,
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
   * Utility function to take all of the values from a given objeect and
   * assign them to this one. This is used for assigning values of search
   * results to the original object.
   */
  public function setValues($object) {
    foreach($object as $property => $value)
    {
        $this->{$property} = $value;
    }
  }

  /**
   * Utility function to add the given Address to the order.
   */
  public function addAddress($address) {
    // Default to create the address..
    $create = TRUE;

    // Look up all addresses for this Contact.
    $result = $this->lookUpId();

    // $result->Addresses will be empty if there are no addresses on the contact.
    if (!empty($result->Addresses)) {

      $result = $result->Addresses;

      // Check each address against the address to be created.
      foreach($result as $contact_addresses) {
        // If they match, then don't create a new address, because it already exits.
        if ($contact_addresses->street1 == $address->street1) {
          $create = FALSE;
        }
      }
    }

    // If none of the addresses matched, then create a new one.
    if ($create) {
      $this->links->Addresses[] = $address;
    }
  }

  /**
   * Utility function to add the given Person to the order.
   */
  public function addPerson($person) {
    // Default to create the person.
    $create = TRUE;

    // Look up all people for this Contact.
    $result = $this->lookUpId();

    // $result->People will be empty if there are no people on the contact.
    if (!empty($result->People)) {

      $result = $result->People;

      // Check each person against the person to be created.
      foreach($result as $people) {
        // If they match, then don't create a new person, because it already exits.
        if ($people->first_name == $person->first_name
          && $people->last_name == $person->last_name) {
          $create = FALSE;
        }
      }
    }

    // If none of the people matched, then create a new one.
    if ($create) {
      $this->links->People[] = $person;
    }
  }

}
