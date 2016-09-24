<?php

include 'NamedEntityRecognizer.php';

$ner = new NamedEntityRecognizer();
var_dump(
    json_encode($ner->parse('dette er en teststring lavet af Bo Henriksen i september 2016'))
    );