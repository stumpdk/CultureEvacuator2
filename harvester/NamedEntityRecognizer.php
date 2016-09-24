<?php

include __DIR__ . DIRECTORY_SEPARATOR .'tokenizerDatasource.php';

class NamedEntityRecognizer {
  private $synonyms = array();

  private $from_year = 1700;
  private $to_year = 2020;

  /**
   * Tries to extract named entities from a piece of text.
   *
   * Please be aware of the following limits:
   *   - Only years between 1700 and 2015 (inclusive) are tagged as years
   *   - Any entity like "Photo:", "By:", etc. is tagges as a byline
   *   - Any eniity ending in -gade, -vej, -pladsen, or -torv is tagged as an
   *     address
   *
   * @param string $text
   *   The string you want to analyse.
   *
   * @return string
   *   Returns a JSON blob with the following possible keys:
   *   - addresses
   *   - bylines
   *   - institutions
   *   - names
   *   - tags
   *   - years
   */
  public function parse($text) {
	$dataSource = new TokenizerDatasource();
	
    $stopwords = $dataSource->get_stopwords();
    $initwords = $dataSource->get_initwords();
    $institutions = $dataSource->get_institutions();
    $names = $dataSource->get_names();

    // tokenize input and remove empty tokens
    $tokens = preg_split("/[\s\?\.,\"'«»\!\!\(\)\\\]/", $text);
    $tokens = array_values(array_filter($tokens));

    $result = array(
      'addresses' => array(),
      'bylines' => array(),
      'institutions' => array(),
      'names' => array(),
      'tags' => array(),
      'years' => array(),
    );
    for ($i = 0; $i < count($tokens); $i++) {
      $entity = array();
      $token = $tokens[$i];

      if ((mb_strtolower($token, 'UTF-8') !== $token || preg_match('/^\d{4}$/', $token)) && !in_array(mb_strtolower($token, 'UTF-8'), $stopwords) && !in_array(mb_strtolower($token, 'UTF-8'), $initwords)) {
        array_push($entity, $token);

        if (isset($tokens[$i + 1])) {
          $token = $tokens[$i + 1];
          while ((mb_strtolower($token, 'UTF-8') !== $token || in_array(mb_strtolower($token, 'UTF-8'), $initwords) || preg_match('/^\d{1,3}$/', $token)) && !in_array(mb_strtolower($token, 'UTF-8'), $stopwords)) {
            $i++;
            array_push($entity, $token);
            if (!isset($tokens[$i + 1])) {
              break;
            }
            $token = $tokens[$i + 1];
          }
        }

        $full_token = implode(' ', $entity);
        if (isset($this->synonyms[$full_token])) {
          $full_token = $this->synonyms[$full_token];
        }

        if (preg_match('/^\d{4}$/', $entity[0])) {
          if ($entity[0] > $this->from_year && $entity[0] < $this->to_year) {
            // the entity is a year (well, any 4-digit number at the moment)
            array_push($result['years'], $entity[0]);
          }
        }
        elseif (preg_match('/^\w+:/', $entity[0])) {
          // the entity looks like a byline, e.g. "Foto: Ole Larsen"
          array_push($result['bylines'], $full_token);
        }
        elseif (in_array($entity[0], $names)) {
          // the first part of the entity is a legal name
          array_push($result['names'], $full_token);
        }
        else {
          if (preg_match('/[a-z]\s+\d+$/', $full_token)) {
            // the entity looks like an address, e.g. "Hovedvejen 42"
            array_push($result['addresses'], $full_token);
          }
          elseif (preg_match('/gade$|vej$|pladsen$|torv$/', $full_token)) {
            // the entity looks like a street name, e.g. ends with "-gade"
            array_push($result['addresses'], $full_token);
          }
          elseif (in_array(mb_strtolower($full_token, 'UTF-8'), $institutions)) {
            // the entity matches the name of an institution
            array_push($result['institutions'], $full_token);
          }
          else {
            array_push($result['tags'], $full_token);
          }
        }
      }
    }

    // remove duplicates and empty bins
    foreach ($result as $key => $value) {
      $result[$key] = array_values(array_unique($result[$key]));
      if (empty($result[$key])) {
        unset($result[$key]);
      }
    }

    return $result;
  }

  public function synonyms($synonyms) {
    $this->synonyms = $synonyms;
  }

  public function from_year($year) {
    $this->from_year = $year;
  }

  public function to_year($year) {
    $this->to_year = $year;
  }
}
