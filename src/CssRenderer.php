<?php

namespace PhpGantt;

class CssRenderer {
  public function render() {
    $css = <<<CSS
<style>
.phpgantt {
  width: 100%;
}

.phpgantt .phpgantt_container {
}

.phpgantt_table_wrapper {
  position: relative;
}

.phpgantt .table_header {
  position: absolute;
  top: 0;
  left: 0;
  width: 390px;
}

.phpgantt .table_body_wrapper {
  overflow-x: scroll;
  margin: 0 0 0 390px;
}

.phpgantt table {
  border-collapse: collapse;
}

.phpgantt td {
  font-size: 12px;
  padding: 4px 2px;
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
  width: 130px;
}

.phpgantt .padding {
  height: 19px;
}

.phpgantt .task_name {
  width: 130px;
}

.phpgantt .assignee {
  width: 130px;
}

.phpgantt .nonbusinessday {
  background: #eeeeee;
}
</style>
CSS;
    echo $css;
  }
}
