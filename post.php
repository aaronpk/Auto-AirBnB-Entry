<?php
require 'vendor/autoload.php';
require 'inc.php';

$config = yaml_parse(file_get_contents('config.yml'));

if($_POST['password'] == $config['master_pass']) {
  foreach($config['locations'] as $l) {
    if($l['name'] == $_POST['location'])
      $location = $l;
  }
  $result = set_door_code($location, $_POST['code'], $location['code_index_0']);  
  echo "Okay!";
} else {
  echo "Wrong password!";
}
