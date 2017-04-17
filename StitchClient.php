<?php

/**
 * A very basic PHP Client for Stitch Labs.
 *
 * Handles authorization and allows performing subsequent chat lookups.
 */

class StitchClient {

  const STITCH_API_OAUTH_TOKEN_URL = 'https://api-pub.stitchlabs.com/oauth/token';
  // Built for version 2 of the API.
  const STITCH_API_BASE_URL = 'https://api-pub.stitchlabs.com/';

  private $client_id;
  private $client_secret;

  public $access_token;


  /**
   * Constructor, set up the things we'll need for every call.
   */
  public function __construct($client_id, $client_secret, $access_token = FALSE) {
    $this->client_id = $client_id;
    $this->client_secret = $client_secret;
    if ($access_token) {
      $this->access_token = $access_token;
    }
  }

  /**
   * Get authentication tokens from Stitchlabs.
   */
  public function authenticate($code, $redirect_uri) {
    $arguments = array(
      'oauth2' => array(
        'grant_type' => 'authorization_code',
        'code' => $code,
        'client_id' => $this->client_id,
        'client_secret' => $this->client_secret,
        'redirect_uri' => $redirect_uri,
      ),
    );

    $response = $this->curlRequest(self::STITCH_API_OAUTH_TOKEN_URL, array(), $arguments);
    $this->access_token = $response->access_token;
    return $response;
  }

  /**
   * Utility function to make all requests.
   */
  public function curlRequest($url, $arguments = array(), $url_params = array(), $headers = array()) {
    $ch = curl_init();

    // All requests after this is set should use the access token.
    if (!empty($this->access_token)) {
      $headers[] = 'access_token: ' . $this->access_token;
    }

    if (!empty($arguments)) {
      $arg_string = json_encode($arguments);

        $headers[] = 'Content-Type: application/json;charset=UTF-8';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arg_string);
    }
    // Oauth2 requires requests be sent using application/x-www-form-urlencoded.
    elseif (!empty($url_params) && !empty($url_params['oauth2'])) {
      $headers[] = 'Content-Type: application/x-www-form-urlencoded';
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($url_params['oauth2'], '', '&'));
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $response = curl_exec($ch);
    $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response_code == 200 || $response_code == 206) {
      return json_decode($response);
    }
    else {
      $error = "Request to $url failed with code $response_code.\n\nResponse:";
      $error .= $response;
      throw new Exception($error);
    }
  }

}
