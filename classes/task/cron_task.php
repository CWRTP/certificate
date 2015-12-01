<?php
namespace mod_certificate\task;

class cron_task extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('crontask', 'mod_certificate');
    }

    public function execute() {
        global $CFG;
        require_once('/var/www/html/moodle/mod/certificate/cron.php');	
    }
}
