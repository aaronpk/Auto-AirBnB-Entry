<?php

function http_get($url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  return curl_exec($ch);
}

function reservation_details($event) {
  $result = [];
  $result['location'] = $event['LOCATION'];
  $result['checkin'] = $event['DTSTART'];
  $result['checkout'] = $event['DTEND'];
  $result['guest'] = $event['SUMMARY'];
  if(preg_match('/PHONE:\s+([0-9 \+\-\(\)]+)\\\/', $event['DESCRIPTION'], $match)) {
    $phone = preg_replace('/[^0-9]/', '', $match[1]);
    $result['phone'] = $phone;
  } else {
    $result['phone'] = false;
  }
  if(preg_match('/EMAIL:\s+([^\\\]+)/', $event['DESCRIPTION'], $match)) {
    // '/' // weird hack for the sublime2 syntax highlighter
    $result['email'] = $match[1];
  } else {
    $result['email'] = false;
  }
  return $result;
}

function set_door_code($location, $code, $index) {
  $data = [
    'num' => $index,
    'code' => $code
  ];

  $ch = curl_init($location['smartthings_endpoint']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $location['smartthings_token']
  ]);
  $result = curl_exec($ch);
  return $result;
}

function send_prowl_notification($event, $message) {
  global $config;

  if(!$config['prowl_key'])
    return;

  $data = [
    'apikey' => $config['prowl_key'],
    'application' => 'AirBnB',
    'event' => $event,
    'description' => $message
  ];

  $ch = curl_init('https://api.prowlapp.com/publicapi/add');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  $response = curl_exec($ch);
}

function send_irc_notification($event, $message) {
  global $config;

  if(!$config['irc_notify'])
    return;

  $data = [
    'content' => '[AirBnB] ' . ($event ? $event . ': ' : '') . $message,
    'channel' => $config['irc_channel']
  ];

  $ch = curl_init($config['irc_notify']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer '.$config['irc_token']
  ]);
  $response = curl_exec($ch);
}

function send_sms_notification($event, $message) {
  global $config;

  foreach($config['email']['recipients'] as $recipient)
    mail($recipient, $event, $message,
      "From: ".$config['email']['from']."\r\nReply-To: ".$config['email']['from']."", '-f'.$config['email']['from']);
}

function send_notification($event, $message) {
  send_irc_notification($event, $message);
  send_prowl_notification($event, $message);
  send_sms_notification($event, $message);
}

