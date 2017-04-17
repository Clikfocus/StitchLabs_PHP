<?php

/**
 * Provides basic functionality for looking up and creating people in Stitch Labs.
 */

class Person {

  const PEOPLE_STITCH_CLASS_NAME = 'People';

  public $dept;
  public $first_name;
  public $id;
  public $last_name;
  public $notes;
  public $primary;
  public $links;

  /**
   * Create a new instance of the Person Object.
   */
  public function __construct() {

  }

}