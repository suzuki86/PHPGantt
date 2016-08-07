<?php

namespace PhpGantt;

require 'vendor/autoload.php';

class YamlParser {
  public static function parse($filename) {
    $value = \Spyc::YAMLLoad($filename);
    return $value;
  }
}
