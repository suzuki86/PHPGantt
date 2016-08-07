<?php

require 'vendor/autoload.php';

use PhpGantt\YamlParser;

class YamlParserTest extends PHPUnit_Framework_TestCase {
  public function testParser() {
    $tmpfile = tmpfile();
    $yaml = <<<YAML
- project:
    name: "Project 1"
    tasks:
      - name: task1
        asignee: asignee1
        startDate: 2016-01-01
        wordload: 5
- project:
    name: "Project 2"
    tasks:
      - name: task2
        asignee: asignee2
        startDate: 2016-01-05
        wordload: 3
      - name: task4
        asignee: asignee3
        dependency: afterPrevious
        wordload: 1
YAML;
    fwrite($tmpfile, $yaml);
    $metaData = stream_get_meta_data($tmpfile);
    $tmpfilePath = $metaData['uri'];
    $yaml = YamlParser::parse(file_get_contents($tmpfilePath));
    $actual = count($yaml);
    $expected = 2;
    $this->assertSame($expected, $actual);
  }
}
