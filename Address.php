<?php

/**
 * Provides basic functionality for looking up and creating addresses in Stitch Labs.
 */

class Address {

  const ADDRESS_STITCH_CLASS_NAME = 'Addresses';

  public $billing;
  public $city;
  public $company;
  public $contact;
  public $country;
  public $country_iso;
  public $id;
  public $notes;
  public $shipping;
  public $state;
  public $street1;
  public $street2;
  public $zip;

  /**
   * Create a new instance of the Person Object
   */
  public function __construct() {

  }

}
