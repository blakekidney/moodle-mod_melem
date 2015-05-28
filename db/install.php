<?php

/**
 * Post installation and migration code.
 *
 * This file replaces:
 *   - STATEMENTS section in db/install.xml
 *   - lib.php/modulename_install() post installation hook
 *   - partially defaults.php
 *
 * @package    mod_melem
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_melem_install() {
    global $CFG;
	require_once($CFG->dirroot.'/mod/melem/locallib.php');
	
	//create the configuration settings with the defaults
	$melemcfg = get_config('melem');
	$defaults = melem_config_defaults();

	foreach($defaults as $name => $value) {
		if(!isset($melemcfg->$name)) {
			set_config($name, $value, 'melem');			
		}
	}
	
}
