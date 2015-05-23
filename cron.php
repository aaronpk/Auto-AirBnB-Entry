<?php
require 'vendor/autoload.php';
require 'inc.php';

$config = yaml_parse(file_get_contents('config.yml'));

foreach($config['locations'] as $location) {
  echo "Processing location: " . $location['name'] . "\n";

  echo "Downloading ics file...\n";
  $calinput = http_get($location['ical']);
  // pre-process the file because the parser doesn't understand split lines
  $calinput = preg_replace('/\n /', '', $calinput);

  $caldata = explode("\n", $calinput);
  $ical = new ICal($caldata);
  $events = $ical->events();
  foreach($events as $event) {
    // Look for any events starting today
    if($event['DTSTART'] == date('Ymd')) {
      $details = reservation_details($event);
      print_r($details);
      if($details['phone']) {
        $code = substr($details['phone'], -4);
        $result = set_door_code($location, $details['phone']);
        print_r($result);
        send_notification('', 'Setting the door code for ' . $details['location'] . ' to ' . $code . ' for ' . $details['guest']);

      } else {
        // No phone number was found!
        send_notification('Error', 'No phone number was found for today\'s reservation (' . $details['guest'] . ' at ' . $details['location'] . ')');
      }
    }
  }
}


