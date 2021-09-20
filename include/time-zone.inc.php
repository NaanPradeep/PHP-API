<?php

if($user_time_zone) {
    $datetime = new DateTime;
    $otherTZ  = new DateTimeZone($user_time_zone);
    $datetime->setTimezone($otherTZ);
    $timestamp = $datetime->format('Y-m-d H:i:s');
    $LOG->info("Time zone changed to ".$user_time_zone);
  } else {
    $datetime = new DateTime;
    $otherTZ  = new DateTimeZone("Asia/Kolkata");
    $datetime->setTimezone($otherTZ);
    $timestamp = $datetime->format('Y-m-d H:i:s');
    $LOG->info("Time zone changed to Asia/Kolkata");
  }