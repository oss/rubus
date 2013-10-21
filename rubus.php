<?php

function build_inverted_index($data)
{
    print_r($data);
    return $data;
}

$abbreviations = json_decode(file_get_contents('abbreviations.json'));

$index = build_inverted_index($abbreviations);
