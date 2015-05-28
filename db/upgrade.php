<?php

/**
 * Melem module upgrade code
 *
 * This file keeps track of upgrades to
 * the resource module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package    mod_url
 * @copyright  2009 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_melem_upgrade($oldversion) {
    global $CFG, $DB;

	$dbman = $DB->get_manager();
	
	$newversion = 2014041100;
    if($oldversion < $newversion) {
		
		$table = new xmldb_table('melem');		
		//new xmldb_field($name, $type=null, $precision=null, $unsigned=null, $notnull=null, $sequence=null, $default=null, $previous=null)
		$field = new xmldb_field('downloadurl', XMLDB_TYPE_TEXT);
		if(!$dbman->field_exists($table, $field)) {
			$dbman->add_field($table, $field);
        }		
		
		//update the module save point
		upgrade_mod_savepoint(true, "$newversion", 'melem');
	}

    return true;
}
