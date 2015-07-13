<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A4_embedded certificate type
 *
 * @package    mod
 * @subpackage certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}

$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Date formatting - can be customized if necessary
$certificatedate = '';
$certificatedate = certificate_get_date($certificate, $certrecord, $course);

$classname = '';

// Print the custom class name
if($certificate->customtext){
         $classname = $certificate->customtext;
} else {
        $classname = $certrecord->classname;
}
//$lenClassname = strlen($classname);
//$lenCN = intval($lenClassname);

//Print the credit hours 
// if($certificate->printhours) {
// $credithours =  $strcredithours.': '.$certificate->printhours;
// } else $credithours = '';

if($certificate->printhours){
        if($certificate->printhours==1){
                $credithours =  'for a total of '.$certificate->printhours.' program hour';
        } else {
                $credithours =  'for a total of '.$certificate->printhours.' program hours';
        }
} else $credithours = '';

if($certificate->eduhours){
        if($credithours == ''){
                if($certificate->eduhours==1){
                        $eduhours =  $certificate->eduhours.' hour of continuing education';
                } else {
                        $eduhours =  $certificate->eduhours.' hours of continuing education';
                }
        } else {
                if($certificate->eduhours==1){
                        $eduhours =  ' or '.$certificate->eduhours.' hour of continuing education';
                } else {
                        $eduhours =  ' or '.$certificate->eduhours.' hours of continuing education';
                }
        }
} else $eduhours = '';

// Print Location
if($certificate->location ==''){
        $location = '';
} else {
        $location = $certificate->location;
}

// Print Trainers
if($certificate->trainer == ''){
        $trainersname = '';
} else {
        $trainersname = 'Presented by '.$certificate->trainer;
}


$customcertificatedate = '';

if($certificate->customdate > 0) {
    $customdate = $certificate->customdate;
} else {
    $customdate = certificate_get_date($certificate, $certrecord, $course);
}

if($certificate->customdate > 0)    {
        if ($certificate->datefmt == 1)    {
                $customcertificatedate = str_replace(' 0', ' ', strftime('%B %d, %Y', $customdate));
        }   if ($certificate->datefmt == 2) {
                $customcertificatedate = date('F jS, Y', $customdate);
        }   if ($certificate->datefmt == 3) {
                $customcertificatedate = str_replace(' 0', '', strftime('%d %B %Y', $customdate));
        }   if ($certificate->datefmt == 4) {
                $customcertificatedate = strftime('%B %Y', $customdate);
        }   if ($certificate->datefmt == 5) {
                $timeformat = get_string('strftimedate');
                $customcertificatedate = userdate($customdate, $timeformat);
        }
}

$customcertificatedate2 = '';
$customdate2 = $certificate->customdate2;
if($certificate->customdate2 > 0)    {
        if ($certificate->datefmt == 1)    {
                $customcertificatedate2 = str_replace(' 0', ' ', strftime('%B %d, %Y', $customdate2));
        }   if ($certificate->datefmt == 2) {
                $customcertificatedate2 = date('F jS, Y', $customdate2);
        }   if ($certificate->datefmt == 3) {
                $customcertificatedate2 = str_replace(' 0', '', strftime('%d %B %Y', $customdate2));
        }   if ($certificate->datefmt == 4) {
                $customcertificatedate2 = strftime('%B %Y', $customdate2);
        }   if ($certificate->datefmt == 5) {
                $timeformat = get_string('strftimedate');
                $customcertificatedate2 = userdate($customdate2, $timeformat);
        }
}


// Print Custom date
$certificateprintdate = "";
if($certificate->customdate == 0){
        if(empty($certificatedate))
        {
                $customdate = ""." ";
        } else {
               $customdate = " on ".$certificatedate;
               $certificateprintdate = $certificatedate;
        }
} else {
        if($certificate->customdate2 == 0){
                $customdate = " on ".$customcertificatedate;
                $certificateprintdate = $customcertificatedate;
        } else {
                $customdate = " from ".$customcertificatedate. " to ".$customcertificatedate2;
                $certificateprintdate = $customcertificatedate;
        }
}

$certificateprintcode = (certificate_get_code($certificate, $certrecord));

// This is to update the certificateprintdate column, so that it can be used in the configurable report
$DB->execute("UPDATE mdl_certificate_issues SET mdl_certificate_issues.certificateprintdate='$certificateprintdate' WHERE mdl_certificate_issues.code='$certificateprintcode'");

// Define variables
// Landscape
if ($certificate->orientation == 'L') {
    $x = 10;
    $y = 30;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
} else { // Portrait
    $x = 10;
    $y = 40;
    $sealx = 150;
    $sealy = 220;
    $sigx = 30;
    $sigy = 230;
    $custx = 30;
    $custy = 230;
    $wmarkx = 26;
    $wmarky = 58;
    $wmarkw = 158;
    $wmarkh = 170;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 210;
    $brdrh = 297;
    $codey = 250;
}
// Add underline AB
$udline = str_pad(' ',9,"_");

// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(0, 0, 120);
certificate_print_text($pdf, $x, $y, 'C', 'freesans', 'B', 30, get_string('title', 'certificate'));
$pdf->SetTextColor(0, 0, 0);
certificate_print_text($pdf, $x, $y + 20, 'C', 'freeserif', '', 20, get_string('certify', 'certificate'));
certificate_print_text($pdf, $x, $y + 36, 'C', 'freeserif', '', 30, fullname($USER). ', License #'.$udline);
certificate_print_text($pdf, $x, $y + 55, 'C', 'freeserif', '', 20, get_string('statement', 'certificate'));
//certificate_print_text($pdf, $x, $y + 72, 'C', 'freeserif', '', 20, $course->fullname);
certificate_print_text($pdf, $x, $y + 72, 'C', 'freeserif', '', 24, $classname);
certificate_print_text($pdf, $x, $y + 100, 'C', 'freeserif', '', 14, $credithours.$eduhours.$customdate);
certificate_print_text($pdf, $x, $y + 110, 'C', 'freeserif', '', 14, $trainersname);
certificate_print_text($pdf, $x, $y + 120, 'C', 'freeserif', '', 14, $location);

certificate_print_text($pdf, $x, $codey, 'C', 'freeserif', '', 10, get_string('verificationcode','certificate').$certificateprintcode);
$i = 0;
if ($certificate->printteacher) {
    //$context = get_context_instance(CONTEXT_MODULE, $cm->id);
    $context = CONTEXT_MODULE::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            certificate_print_text($pdf, $sigx, $sigy + ($i * 4), 'L', 'freeserif', '', 12, fullname($teacher));
        }
    }
}

//certificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $certificate->customtext);
?>
