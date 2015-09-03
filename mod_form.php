<?php

/**
 * Melem configuration form
 *
 * @package    mod_melem
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/melem/locallib.php');

class mod_melem_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;
		
		$config = melem_get_config();
		
        //-------------------------------------------------------
        //GENERAL
		$mform->addElement('header', 'general', get_string('general', 'form'));
        //name input
		$mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        //-------------------------------------------------------
        //MEDIA URLS
		$mform->addElement('header', 'mediaurlheader', get_string('mediaurlheader', 'melem'));
		$mform->setExpanded('mediaurlheader');
		
        //type
		$typegroup=array();
		$typegroup[] =& $mform->createElement('radio', 'type', '', get_string('video', 'melem'), 'video');
		$typegroup[] =& $mform->createElement('radio', 'type', '', get_string('audio', 'melem'), 'audio');
		$mform->addGroup($typegroup, 'mediatype', get_string('type', 'melem'), array('&nbsp;&nbsp;&nbsp; '), false);
		$mform->addRule('mediatype', null, 'required', null, 'client');
		
		//source 1
		$mform->addElement('text', 'src1', get_string('src1', 'melem'), array('style'=>'width:95%'));
        $mform->setType('src1', PARAM_URL);
        $mform->addRule('src1', null, 'required', null, 'client');
		$mform->addHelpButton('src1', 'src1', 'melem');
		
		//source 2
		if(!$config->allow_onesource) {
			$mform->addElement('text', 'src2', get_string('src2', 'melem'), array('style'=>'width:95%'));
			$mform->setType('src2', PARAM_URL);    
			$mform->addHelpButton('src2', 'src2', 'melem');    
		}
		//source 3
		if(!$config->allow_onesource) {
			$mform->addElement('text', 'src3', get_string('src3', 'melem'), array('style'=>'width:95%'));
			$mform->setType('src3', PARAM_URL);
			$mform->addHelpButton('src3', 'src3', 'melem');
		}
		
		//-------------------------------------------------------
        //MEDIA OPTIONS
		
		//only show the header if any of the options are permitted to be shown
		if($config->allow_maxwidth ||
		   $config->allow_autoplay ||
		   $config->allow_controls ||
		   $config->allow_preload ||
		   $config->allow_downloadlink ||
		   $config->allow_poster) {
				
			$mform->addElement('header', 'mediaoptsheader', get_string('mediaoptsheader', 'melem'));
		}
		
		//maxwidth
		if($config->allow_maxwidth) {		
			$mform->addElement('text', 'maxwidth', get_string('maxwidth', 'melem'));
			$mform->setType('maxwidth', PARAM_INT);
			$mform->addHelpButton('maxwidth', 'maxwidth', 'melem');
			$mform->setDefault('maxwidth', $config->maxwidth);
		}
			
		//autoplay
		if($config->allow_autoplay) {
			$autoplay=array();
			$autoplay[] =& $mform->createElement('radio', 'autoplay', '', get_string('yes', 'melem'), '1');
			$autoplay[] =& $mform->createElement('radio', 'autoplay', '', get_string('no', 'melem'), '0');
			$mform->addGroup($autoplay, 'autoplaygroup', get_string('autoplay', 'melem'), array('&nbsp;&nbsp;&nbsp; '), false);
			$mform->addHelpButton('autoplaygroup', 'autoplay', 'melem');
			$mform->setDefault('autoplay', $config->autoplay);
		}
		
		//controls
		if($config->allow_controls) {
			$controls=array();
			$controls[] =& $mform->createElement('radio', 'controls', '', get_string('yes', 'melem'), '1');
			$controls[] =& $mform->createElement('radio', 'controls', '', get_string('no', 'melem'), '0');
			$mform->addGroup($controls, 'controlsgroup', get_string('controls', 'melem'), array('&nbsp;&nbsp;&nbsp; '), false);
			$mform->addHelpButton('controlsgroup', 'controls', 'melem');
			$mform->setDefault('controls', $config->controls);
		}
		
		//preload
		if($config->allow_preload) {
			$mform->addElement('select', 'preload', get_string('preload', 'melem'), melem_get_preload_options());
			$mform->addHelpButton('preload', 'preload', 'melem');
			$mform->setDefault('controlsgroup', $config->preload);
		}
		
		//downloadlink
		if($config->allow_downloadlink) {
			$downlink=array();
			$downlink[] =& $mform->createElement('radio', 'downloadlink', '', get_string('yes', 'melem'), '1');
			$downlink[] =& $mform->createElement('radio', 'downloadlink', '', get_string('no', 'melem'), '0');
			$mform->addGroup($downlink, 'downloadlinkgroup', get_string('downloadlink', 'melem'), array('&nbsp;&nbsp;&nbsp; '), false);
			$mform->addHelpButton('downloadlinkgroup', 'downloadlink', 'melem');
			$mform->setDefault('downloadlink', $config->downloadlink);
			
			$mform->addElement('text', 'downloadurl', get_string('downloadurl', 'melem'), array('style'=>'width:95%'));
			$mform->setType('downloadurl', PARAM_URL);    
			$mform->addHelpButton('downloadurl', 'downloadurl', 'melem'); 
		}
		
		//poster
		if($config->allow_poster) {		
			/*
			$mform->addElement('filepicker', 'poster', get_string('poster', 'melem'), null, array('accepted_types' => 'jpg, png, gif'));
			$mform->addHelpButton('poster', 'poster', 'melem');
			*/
			$poster_options = array(
				'subdirs' => 0, 	//Are subdirectories allowed? (true or false) 
				'maxbytes' => 0, 	//Restricts the total size of all the files. 
				'maxfiles' => 1,    //Restricts the total number of files.         						  
				'accepted_types' => array('image')		
			);
			$mform->addElement('filemanager', 'posterimage', get_string('poster', 'melem'), null, $poster_options);
			$mform->addHelpButton('posterimage', 'poster', 'melem');
		}
		
        //-------------------------------------------------------
        //COMMON MODULE SETTINGS
		$this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        
		$config = melem_get_config();
		
		//load files into their draft areas
		if($this->current->instance && $config->allow_poster) {
            //poster image
			$draftitemid = file_get_submitted_draft_itemid('posterimage');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_melem', 'poster', 0, array('subdirs'=>false));
            $default_values['posterimage'] = $draftitemid;
        }
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        return $errors;
    }

}
