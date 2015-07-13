<?php

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/querylib.php');
require_once($CFG->dirroot.'/lib/pdflib.php');
require_once($CFG->libdir . '/completionlib.php');

global $DB;

$cronlist = certificate_cronlist2();

foreach($cronlist as $cron)
{
	echo '</br>';
	$course = $DB->get_record('course', array('id'=>$cron->course));
	echo $course->fullname.'</br>';
	$cm = $DB->get_record('course_modules', array('id'=>$cron->cmid));
	$certificate = $DB->get_record('certificate', array('id'=>$cron->instance));
	echo $cm->id.'</br>';
	$user = $DB->get_record('user', array('id'=>$cron->userid));
	echo 'username: '.$user->firstname.' '.$user->lastname.'</br>';
	//$context = get_context_instance(CONTEXT_MODULE, $cron->cmid);
        $context = CONTEXT_MODULE::instance($cron->cmid);
	
	// Create new certificate record
	$certrecord = certificate_prepare_issue($course, $user, $certificate);
	echo 'certdate: '.$certrecord->certdate.'</br>';

	//Load some strings
	// Load some strings
	$strreviewcertificate = get_string('reviewcertificate', 'certificate');
	$strgetcertificate = get_string('getcertificate', 'certificate');
	$strgrade = get_string('grade', 'certificate');
	$strcoursegrade = get_string('coursegrade', 'certificate');
	$strcredithours = get_string('credithours', 'certificate');
	$filename = clean_filename($certificate->name.'.pdf');
	
	if($certificate->certificatetype=='Letter_CEU')
	{
		
	}
	//Non_letter_CEU
	else
	{
		
	}
	
	if($certificate->reissuecert)
	{
		certificate_issue2($course, $certificate, $certrecord, $cm, $user); // update certrecord as issued
	}
	else if($certrecord->certdate==0)
	{
		certificate_issue2($course, $certificate, $certrecord, $cm, $user); // update certrecord as issued
	}
	
	if ($certificate->savecert == 1) {
		//pdf contents are now in $file_contents as a string
		$file_contents = $pdf->Output('', 'S');
		$filename = clean_filename($certificate->name.'.pdf');
		certificate_save_pdf_cron($file_contents, $certrecord->id, $filename, $context->id);
	}
	
	if ($certificate->delivery == 2) {
		certificate_email_students($user, $course, $certificate, $certrecord, $context);
	}
	
	
	
	
// 	echo $certificate->certificatetype;
	if ($certrecord->certdate > 0) {
		$certdate = $certrecord->certdate;
	}
	else $certdate = certificate_generate_date2($certificate, $course, $user);
	if($certificate->printdate > 0)    {
				if ($certificate->datefmt == 1)    {
					$certificatedate = str_replace(' 0', ' ', strftime('%B %d, %Y', $certdate));
				}   if ($certificate->datefmt == 2) {
					$certificatedate = date('F jS, Y', $certdate);
				}   if ($certificate->datefmt == 3) {
					$certificatedate = str_replace(' 0', '', strftime('%d %B %Y', $certdate));
				}   if ($certificate->datefmt == 4) {
					$certificatedate = strftime('%B %Y', $certdate);
				}   if ($certificate->datefmt == 5) {
					$timeformat = get_string('strftimedate');
					$certificatedate = userdate($certdate, $timeformat);
				}
			}
			echo 'printdate'.$certificate->printdate.'------'.'datefmt: '.$certificate->datefmt.'</br>';
			echo 'certificatedate: '.$certificatedate.'</br>';
	
}
