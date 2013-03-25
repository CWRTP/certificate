<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/certificate/lib.php');

class mod_certificate_mod_form extends moodleform_mod {

    function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('certificatename', 'certificate'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $this->add_intro_editor(false, get_string('intro', 'certificate'));

        // Issue options
        $mform->addElement('header', 'issueoptions', get_string('issueoptions', 'certificate'));
        $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'emailteachers', get_string('emailteachers', 'certificate'), $ynoptions);
        $mform->setDefault('emailteachers', 0);
        $mform->addHelpButton('emailteachers', 'emailteachers', 'certificate');

        $mform->addElement('text', 'emailothers', get_string('emailothers', 'certificate'), array('size'=>'40', 'maxsize'=>'200'));
        $mform->setType('emailothers', PARAM_TEXT);
        $mform->addHelpButton('emailothers', 'emailothers', 'certificate');

        $deliveryoptions = array( 0 => get_string('openbrowser', 'certificate'), 1 => get_string('download', 'certificate'), 2 => get_string('emailcertificate', 'certificate'));
        $mform->addElement('select', 'delivery', get_string('delivery', 'certificate'), $deliveryoptions);
        $mform->setDefault('delivery', 0);
        $mform->addHelpButton('delivery', 'delivery', 'certificate');

        $mform->addElement('select', 'savecert', get_string('savecert', 'certificate'), $ynoptions);
        $mform->setDefault('savecert', 0);
        $mform->addHelpButton('savecert', 'savecert', 'certificate');

        $reportfile = "$CFG->dirroot/certificates/index.php";
        if (file_exists($reportfile)) {
            $mform->addElement('select', 'reportcert', get_string('reportcert', 'certificate'), $ynoptions);
            $mform->setDefault('reportcert', 0);
            $mform->addHelpButton('reportcert', 'reportcert', 'certificate');
        }

        $mform->addElement('select', 'reissuecert', get_string('reissuecert', 'certificate'), $ynoptions);
        $mform->setDefault('reissuecert', 0);
        $mform->addHelpButton('reissuecert', 'reissuecert', 'certificate');

        // Auto Generation Options
        $mform->addElement('header', 'autogenoptions', get_string('autogenoptions', 'certificate'));
        $mform->addElement('select', 'autogen', get_string('autogen', 'certificate'), $ynoptions);
        $mform->setDefault('autogen',0);
        $mform->addHelpButton('autogen','autogen','certificate');
        
        // Text Options
        $mform->addElement('header', 'textoptions', get_string('textoptions', 'certificate'));

        $modules = certificate_get_mods();
        $dateoptions = certificate_get_date_options() + $modules;
        $mform->addElement('select', 'printdate', get_string('printdate', 'certificate'), $dateoptions);
        $mform->setDefault('printdate', 'N');
        $mform->addHelpButton('printdate', 'printdate', 'certificate');

        // Add Custom Start date Options
        $options = array('startyear => 1990', 'stopyear' => '2050', 'timezone' => 99, 'applydst' => true, 'optional' => true);
        $mform->addElement('date_selector', 'customdate', get_string('customdate', 'certificate'), $options);
        $mform->setType('customdate', PARAM_RAW);
        $mform->addHelpButton('customdate', 'customdate', 'certificate');
        
        // Add Custom End date Options
        $options = array('startyear => 1990', 'stopyear' => '2050', 'timezone' => 99, 'applydst' => true, 'optional' => true);
        $mform->addElement('date_selector', 'customdate2', get_string('customdate2', 'certificate'), $options);
        $mform->setType('customdate2', PARAM_RAW);
        $mform->addHelpButton('customdate2', 'customdate2', 'certificate');
        
        $dateformatoptions = array( 1 => 'January 1, 2000', 2 => 'January 1st, 2000', 3 => '1 January 2000', 4 => 'January 2000', 5 => get_string('userdateformat', 'certificate'));
        $mform->addElement('select', 'datefmt', get_string('datefmt', 'certificate'), $dateformatoptions);
        $mform->setDefault('datefmt', 0);
        $mform->addHelpButton('datefmt', 'datefmt', 'certificate');

        $mform->addElement('select', 'printnumber', get_string('printnumber', 'certificate'), $ynoptions);
        $mform->setDefault('printnumber', 0);
        $mform->addHelpButton('printnumber', 'printnumber', 'certificate');

        $gradeoptions = certificate_get_grade_options() + $modules;
        $mform->addElement('select', 'printgrade', get_string('printgrade', 'certificate'),$gradeoptions);
        $mform->setDefault('printgrade', 0);
        $mform->addHelpButton('printgrade', 'printgrade', 'certificate');

        $gradeformatoptions = array( 1 => get_string('gradepercent', 'certificate'), 2 => get_string('gradepoints', 'certificate'), 3 => get_string('gradeletter', 'certificate'));
        $mform->addElement('select', 'gradefmt', get_string('gradefmt', 'certificate'), $gradeformatoptions);
        $mform->setDefault('gradefmt', 0);
        $mform->addHelpButton('gradefmt', 'gradefmt', 'certificate');

        $outcomeoptions = certificate_get_outcomes();
        $mform->addElement('select', 'printoutcome', get_string('printoutcome', 'certificate'),$outcomeoptions);
        $mform->setDefault('printoutcome', 0);
        $mform->addHelpButton('printoutcome', 'printoutcome', 'certificate');

        // Program hours
        $mform->addElement('text', 'printhours', get_string('printhours', 'certificate'), array('size'=>'5'));
        $mform->setType('printhours', PARAM_TEXT);
        $mform->addHelpButton('printhours', 'printhours', 'certificate');

        // CEU Hours
        $mform->addElement('text', 'eduhours', get_string('eduhours', 'certificate'), array('size'=>'5'));
        $mform->setType('eduhours', PARAM_TEXT);
        $mform->addHelpButton('eduhours', 'eduhours', 'certificate');
        
//         $mform->addElement('select', 'printteacher', get_string('printteacher', 'certificate'), $ynoptions);
//         $mform->setDefault('printteacher', 0);
//         $mform->addHelpButton('printteacher', 'printteacher', 'certificate');

        // Instead of print teacher, you able to enter custom trainers' name.
        $mform->addElement('textarea', 'trainer', get_string('trainer', 'certificate'), array('cols'=>'40', 'rows'=>'1', 'wrap'=>'virtual'));
        $mform->setType('location', PARAM_RAW);
        $mform->addHelpButton('trainer', 'trainer', 'certificate');
        
        // Custom Course name instead of custom text.
        $mform->addElement('textarea', 'customtext', get_string('customtext', 'certificate'), array('cols'=>'40', 'rows'=>'4', 'wrap'=>'virtual'));
        $mform->setType('customtext', PARAM_RAW);
        $mform->addHelpButton('customtext', 'customtext', 'certificate');

        // Add Custom location field
        $mform->addElement('textarea', 'location', get_string('location', 'certificate'), array('cols'=>'40', 'rows'=>'1', 'wrap'=>'virtual'));
        $mform->setType('location', PARAM_RAW);
        $mform->addHelpButton('location', 'location', 'certificate');
        
        // Design Options
        $mform->addElement('header', 'designoptions', get_string('designoptions', 'certificate'));
        $CERTIFICATE_TYPES = certificate_types();
        $mform->addElement('select', 'certificatetype', get_string('certificatetype', 'certificate'),$CERTIFICATE_TYPES);
        $mform->setDefault('certificatetype', 'letter_CEUs');
        $mform->addHelpButton('certificatetype', 'certificatetype', 'certificate');

        $orientation = array( 'L' => get_string('landscape', 'certificate'), 'P' => get_string('portrait', 'certificate'));
        $mform->addElement('select', 'orientation', get_string('orientation', 'certificate'), $orientation);
        $mform->setDefault('orientation', 'landscape');
        $mform->addHelpButton('orientation', 'orientation', 'certificate');

		$borderstyleoptions = certificate_get_borders();
        $mform->addElement('select', 'borderstyle', get_string('borderstyle', 'certificate'), $borderstyleoptions);
        $mform->setDefault('borderstyle', 0);
        $mform->addHelpButton('borderstyle', 'borderstyle', 'certificate');

        $printframe = array( 0 => get_string('no'), 1 => get_string('borderblack', 'certificate'), 2 => get_string('borderbrown', 'certificate'), 3 => get_string('borderblue', 'certificate'), 4 => get_string('bordergreen', 'certificate'));
        $mform->addElement('select', 'bordercolor', get_string('bordercolor', 'certificate'), $printframe);
        $mform->setDefault('bordercolor', 1);
        $mform->addHelpButton('bordercolor', 'bordercolor', 'certificate');

        $wmarkoptions = certificate_get_watermarks();
        $mform->addElement('select', 'printwmark', get_string('printwmark', 'certificate'),$wmarkoptions);
        $mform->setDefault('printwmark', 0);
        $mform->addHelpButton('printwmark', 'printwmark', 'certificate');

        $signatureoptions = certificate_get_signatures ();
        $mform->addElement('select', 'printsignature', get_string('printsignature', 'certificate'), $signatureoptions);
        $mform->setDefault('printsignature', 0);
        $mform->addHelpButton('printsignature', 'printsignature', 'certificate');

        $sealoptions = certificate_get_seals();
        $mform->addElement('select', 'printseal', get_string('printseal', 'certificate'),$sealoptions);
        $mform->setDefault('printseal', 0);
        $mform->addHelpButton('printseal', 'printseal', 'certificate');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();

    }
}