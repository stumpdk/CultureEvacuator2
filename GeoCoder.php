<?php

class GeoCoder {
  /**
   * Uses Kortforsyningen to geocode an address. Hardcoded to only search
   * for positions in København and Frederiksberg (see the 'komkode' option).
   *
   * @param string $address
   *   The address you want to geocode.
   *
   * @return array
   *   Returns a (lng, lat) array on success, FALSE otherwise.
   */
  public function geocode($address) {
    preg_match('/^(.*?)\s+(\d+)/', $address, $matches);

    $street = $matches[1];
    $number = $matches[2];

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
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close ($ch);

    $result = json_decode($server_output);
    $compare = (array) $result;

    if (empty($compare) or $http_status != 200) {
      return FALSE;
    }
    else {
      return $result->features[0]->geometry->coordinates;
    }
  }
}
