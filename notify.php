<?php
require 'vendor/autoload.php';
require 'inc.php';

$config = yaml_parse(file_get_contents('config.yml'));

$input = json_decode(file_get_contents('php://input'));

file_put_contents(dirname(__FILE__).'/logs/'.date('Ymd-His').'.txt', json_encode($input, JSON_PRETTY_PRINT));

if(property_exists($input, type) && $input->type == 'CodeReport') {
  send_notification('', $input->locationName . ' door code was set to ' . $input->code);
}
