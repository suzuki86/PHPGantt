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
});
</script>
JS;
    echo $js;
  }
}
