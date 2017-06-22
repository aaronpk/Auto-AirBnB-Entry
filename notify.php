<?php
require 'vendor/autoload.php';
require 'inc.php';

$config = yaml_parse(file_get_contents('config.yml'));

$input = json_decode(file_get_contents('php://input'));

#list($m,$s) = explode(' ',microtime());
#file_put_contents(dirname(__FILE__).'/logs/'.date('Ymd-His').'.'.round($m*1000000).'.txt', json_encode($input, JSON_PRETTY_PRINT));

if(property_exists($input, type) && $input->type == 'CodeReport') {
  if($input->code) {
    $message = (property_exists($input, 'doorName') ? $input->doorName : $input->locationName) . ' code was set to ' . $input->code;
  } else {
	$message = $input->descriptionText;
  }
  send_irc_notification('', $message . ' (code ' . $input->num . ') ' . json_encode($input));
  send_prowl_notification('', $message);
  send_sms_notification('', $message);
}
