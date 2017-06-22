<?php
require 'vendor/autoload.php';
require 'inc.php';
chdir(dirname(__FILE__));

$config = yaml_parse(file_get_contents('config.yml'));

echo date('Y-m-d H:i:s')."\n";

foreach($config['locations'] as $location) {
  echo "Processing location: " . $location['name'] . "\n";

  echo "Downloading ics file...\n";
  $calinput = http_get($location['ical']);
  // pre-process the file because the parser doesn't understand split lines
  $calinput = preg_replace('/\n /', '', $calinput);

  $caldata = explode("\n", $calinput);
  $ical = new ICal($caldata);
  $events = $ical->events();

  $codeindex = 0;

  foreach($events as $event) {
    // Look for any events starting today
    if($event['DTSTART'] == date('Ymd') /* || $event['DTEND'] == date('Ymd') */) {
      $details = reservation_details($event);

      if($details['phone']) {
        $code = substr($details['phone'], -4);
        $index = $location['code_index_'.$codeindex];
        $codeindex++;
        $result = set_door_code($location, $code, $index);
        echo "Setting code $index to $code\n";
        print_r($result);
        echo "\n";

        try {
          $response = json_decode($result);
          if(property_exists($response,'code')) {
            $notification = 'Setting the door code for ' . $details['location'] . ' to ' . $code . ' for ' . $details['guest'];
          } else {
            $notification = "Failed to set the door code for ".$location['name'].'. SmartThings did not acknowledge the code';
          }
        } catch(Exception $e) {
            $notification = "Failed to set the door code for ".$location['name'].': '.$e->getMessage();
        }
        send_irc_notification('', $notification);
        send_prowl_notification('', $notification);
        // send_sms_notification('', $notification);
        sleep(30);

      } else {
        // No phone number was found!
        send_irc_notification('', 'No phone number was found for ' . $details['guest'] . ' (at ' . $location['name'] . ')');
      }
    }
  }
  
  echo "Done\n\n";
}


