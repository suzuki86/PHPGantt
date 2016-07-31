<?php

namespace PhpGantt;

class JsRenderer {
  public function render() {
    $js = <<<JS
<script>
$(function(){
  $('.phpgantt .phpgantt_container .phpgantt_table_wrapper').each(function(){
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
