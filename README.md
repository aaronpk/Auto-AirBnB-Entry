DEPRECATED: AirBnB stopped providing the last 4 digits of the guest's phone number in the calendar feed in 2019 so this code no longer works.



Auto AirBnB Entry
=================

Sets the door code to the last 4 digits of the guest's phone number before they arrive.


Requirements
------------

* One or more AirBnB listings that you manage, you'll need the iCal URLs for the bookings
* A Schlage FE595 door lock connected to a SmartThings hub for each AirBnB


How it Works
------------

The cron job runs each morning. It downloads the latest .ics file from AirBnB to get the 
latest booking info. When there is a guest arriving today, it will find their phone number
from the ics file, and set the unlock code to the last 4 digits by making an HTTP request
to the SmartThings API. 

If there is a guest checking out that day, their code will still work for the day, by setting
a secondary entry on the lock to their phone number. On days where only one guest should 
have access, the code is set to your default secret code defined in the config file.


Setup
-----

### SmartThings App 

You'll first need to install the SmartThings app on your account. Copy the `lock.groovy`
script to a new app in the SmartThings developer portal. When you make the app, be sure
to check the "OAuth" checkbox. 

(If you want to receive IRC notifications when the code is changed, uncomment the 
`sendPostRequest` function call on like 75 and add your IRC bot proxy URL in the `sendPostRequest`
method. See https://github.com/zenirc for more info on running an IRC bot.)


### Web App

Install the web app on a server that supports PHP. Set up a cron job to run `cron.php` every 
morning in your local time.

Copy `config.template.yml` to `config.yml` and add your settings. You'll need to define
one or more AirBnB locations including the name, ical URL, as well as adding the SmartThings
app endpoint and token for each location.

You can optionally add a Prowlapp key or IRC proxy URL to receive notifications when 
the cron job runs. If you don't add these, everything will still work fine, and you will
still get a push notifications from SmartThings when a code is updated.

![push notifications](https://farm8.staticflickr.com/7718/18019975421_536c216470_z.jpg)

