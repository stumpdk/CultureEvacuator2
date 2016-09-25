<?php

include 'NamedEntityRecognizer.php';

$ner = new NamedEntityRecognizer();

$data = file_get_contents($argv[1]);

var_dump(
    json_encode($ner->parse($data))
    );