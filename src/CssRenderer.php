<?php

namespace PhpGantt;

class CssRenderer {
  public function render() {
    $css = <<<CSS
<style>
.phpgantt {
  width: 100%;
  position: relative;
}

.phpgantt .phpgantt_container {
  overflow-x: scroll;
  margin: 0 0 0 465px;
}

.phpgantt table {
  border-collapse: collapse;
}

.phpgantt td {
  font-size: 12px;
  padding: 3px 2px;
  border: 1px solid #cccccc;
}

.phpgantt .cell_header {
  text-align: center;
}

.phpgantt .cell {
  text-align: center;
  width: 40px;
}

.phpgantt .project_name {
  width: 150px;
  position: absolute;
  top: auto;
  left: 0;
}

.phpgantt .padding {
  height: 12px;
}

.phpgantt .task_name {
  width: 150px;
  position: absolute;
  top: auto;
  left: 155px;
}

.phpgantt .assignee {
  width: 150px;
  position: absolute;
  top: auto;
  left: 310px;
}

.phpgantt .nonbusinessday {
  background: #eeeeee;
}
</style>
CSS;
    echo $css;
  }
}
