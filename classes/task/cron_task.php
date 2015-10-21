<?php
namespace mod_certificate\task;
defined('MOODLE_INTERNAL') || die();
class cron_task extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('crontask', 'mod_certificate');
    }

    public function execute() {
        global $CFG;
        require_once($CFG->dirroot.'/mod/certificate/cron.php');	
	    }
}
