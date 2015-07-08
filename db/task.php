<?php

defined('MOODLE_INTERNAL') || die();
$tasks = array(
    array(
        'classname' => 'mod_certificate\task\cron_task',
        'blocking'  => 0,
        'minute'    => '*/10',
        'hour'      => '*',
        'day'       => '*',
        'month'     => '*',
        'dayofweek' => '*'
    )
);
