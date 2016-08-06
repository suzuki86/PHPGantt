<?php

namespace PhpGantt;

class JsRenderer {
  public function render() {
    $js = <<<JS
<script>
$(function(){
  $('.phpgantt .table_body').each(function(){
    var cellCount = $(this).find('tr:first>td').length - 3;
    var wrapperWidth = 45 * cellCount;
    $(this).css({ width: wrapperWidth});
  });
  $('.phpgantt .table_header tr').each(function(){
    var rowHeight = $(this).height();
    var rowNumber = $(this).index();
    $('.phpgantt .table_body tr').eq(rowNumber).css({ height: rowHeight});
  });
});
</script>
JS;
    echo $js;
  }
}
