<?php
  function my_log($text){
    file_put_contents('/tmp/phplogtest', $text."\r\n", FILE_APPEND | LOCK_EX);
  }
?>
