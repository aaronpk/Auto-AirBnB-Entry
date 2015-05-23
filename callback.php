<?php
require 'vendor/autoload.php';
require 'inc.php';

$config = yaml_parse(file_get_contents('config.yml'));

$input = json_decode(file_get_contents('php://input'));


