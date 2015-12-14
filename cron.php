<?php
//define('CLI_SCRIPT', true);
require_once('/var/www/html/moodle/config.php');
require_once('/var/www/html/moodle/course/lib.php');
require_once('/var/www/html/moodle/grade/lib.php');
require_once('/var/www/html/moodle/grade/querylib.php');
require_once('/var/www/html/moodle/lib/pdflib.php');
//require_once('/var/www/html/moodle/lib/conditionlib.php');
require_once('/var/www/html/moodle/lib/adodb/adodb.inc.php');
require_once('/var/www/html/moodle/mod/certificate/lib.php');

local_certificate();
function local_certificate() {
    global $DB, $CFG, $USER, $COURSE;
    //require_once($CFG->libdir . '/conditionlib.php');
    //require_once("$CFG->libdir/pdflib.php");
    //require_once($CFG->dirroot . '/mod/certificate/lib.php');
    //require_once($CFG->libdir . '/adodb/adodb.inc.php');

    echo "------------- Finding New Certificates ------------------\n\n";

    $courseids = $DB->get_records_sql("SELECT DISTINCT c.id FROM {certificate} module, {course} c WHERE module.course=c.id and module.autogen = 1");


    $module = $DB->get_record('modules', array('name' => 'certificate'));


    $courses = array();
    if (!empty($courseids)) {
        foreach ($courseids as $courseid) {
            $courses[$courseid->id] = $DB->get_record('course', array('id' => $courseid->id));
            if ($courses[$courseid->id]->visible != 1) {
                unset($courses[$courseid->id]);
            }
        }
    }

    foreach ($courses as $cid => $course) {
        $COURSE = $course;
        echo "\n\n--------------------------\nCourse: {$course->shortname}\n";
			
        $param = array();
        $param['courseid'] = $cid;
        $param['guestid'] = $CFG->siteguest;
        $param['roleshortname'] = 'student';

		
        $students = $DB->get_records_sql("SELECT usr.id, usr.idnumber, usr.firstname, usr.lastname, usr.email FROM {course} c
                                INNER JOIN {context} cx ON c.id = cx.instanceid
                                AND cx.contextlevel = " . CONTEXT_COURSE . " and c.id = :courseid
                                INNER JOIN {role_assignments} ra ON cx.id = ra.contextid
                                INNER JOIN {role} r ON ra.roleid = r.id
                                INNER JOIN {user} usr ON ra.userid = usr.id
                                WHERE r.shortname = :roleshortname AND
                                usr.id <> :guestid AND usr.deleted = 0 AND usr.confirmed = 1
                                ORDER BY usr.firstname, c.fullname", $param);
        $sql = "SELECT cm.id, cm.instance FROM {course_modules} cm INNER JOIN {certificate} c ON c.id = cm.instance WHERE cm.course = $cid AND cm.module = $module->id AND c.autogen = 1";
        $certmods = $DB->get_records_sql($sql);
	foreach ($certmods as $mod) {
            $certificate = $DB->get_record("certificate", array('id' => $mod->instance , 'autogen' => 1));
            echo "\nCERT: {$certificate->name}\n";
            $cert_count = 0;
            $certificate_issued_users = $DB->get_records("certificate_issues", array('certificateid' => $certificate->id), '', 'userid');
            foreach ($students as $student) {
                if (empty($certificate_issued_users) || !array_key_exists($student->id, $certificate_issued_users)) {

                    $modinfo = get_fast_modinfo($course, $student->id);
                    $cm = $modinfo->get_cm($mod->id);
                    $context = CONTEXT_MODULE::instance($cm->id);

                    // now create any certs
		    $info = new \core_availability\info_module($cm);
	            $available = $cm->availableinfo;
                    if ($available) {
                        $USER = $student;
			if ($certificate->requiredtime && !has_capability('mod/certificate:manage', $context)) {
			    if (certificate_get_course_time($course->id) < ($certificate->requiredtime * 60)) {
				continue;
                            }
                        }
                        $certrecord = certificate_get_issue($course, $student, $certificate, $cm);
                        make_cache_directory('tcpdf');
                        // Load the specific certificate type.
                        require("$CFG->dirroot/mod/certificate/type/$certificate->certificatetype/certificate.php");
                        $certname = rtrim($certificate->name, '.');
                        $filename = clean_filename("$certname.pdf");
                        $file_contents = $pdf->Output('', 'S');
                        certificate_save_pdf($file_contents, $certrecord->id, $filename, $context->id);

                        //certificate_email_student($course, $certificate, $certrecord, $context);
                        //$pdf->Output('', 'S'); // send
                        //add_to_log($course->id, 'certificate', 'email send', "con.php", $certificate->id, $student->id);
                        $cert_count++;
                    }
                }
                echo "Student id = $student->id Certificate Generated\n\n"; 
            }
        }
    }
    echo "\n\n------------- END Plugin ------------------\n";
}
?>
