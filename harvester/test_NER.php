<?php

include 'NamedEntityRecognizer.php';

$ner = new NamedEntityRecognizer();
var_dump($ner->parse('dette er en teststring lavet af Bo Henriksen'));