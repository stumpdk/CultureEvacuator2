<?php

class GeoCoder {
  /**
   * Uses Kortforsyningen to geocode an address. Hardcoded to only search
   * for positions in København and Frederiksberg (see the 'komkode' option).
   *
   * @param string $streetname
   *   The name of the street you are trying to geocode.
   *
   * @param string $streetname
   *   The name of the street you are trying to geocode.
   *
   * @return array
   *   Returns a (lng, lat) array.
   */
  public function geocode($street, $number) {
    // set endpoint url
    $url = 'http://kortforsyningen.kms.dk/';

    // set request options, chagne 'komkode' to search another part of the country
    $options = array(
    	'servicename'  => 'RestGeokeys_v2',
    	'method' => 'adresse',
    	'vejnavn' => $street,
    	'husnr' => $number,
    	'komkode' => '0101;0147',
    	'geometry' => 'true',
    	'georef' => 'EPSG:4326',
      'login' => Config::KMSuser,
      'password' => Config::KMSpass,
    );

    // init curl and set basic options
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    // perform request and close the connection
    $server_output = curl_exec($ch);
    curl_close ($ch);

    // decode the response
    $result = json_decode($server_output);

    return $result->features[0]->geometry->coordinates;
  }
}
