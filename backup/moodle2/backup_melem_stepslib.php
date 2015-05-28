<?php

/**
 * Define all the backup steps that will be used by the backup_melem_activity_task
 *
 * @package    	mod_melem
 * @copyright  	2014 Blake Kidney
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete melem structure for backup, with file and id annotations
 */
class backup_melem_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the melem module stores no user info

        // Define each element separated
        $melem = new backup_nested_element('melem', array('id'), array(
            'name', 'type', 'src1', 'src2', 'src3', 'maxwidth', 'autoplay', 'controls', 'preload',
			'downloadlink', 'downloadurl', 'posterurl', 'intro', 'introformat', 'display', 'displayoptions', 'timemodified'));


        // Build the tree
        // (none)

        // Define sources
        $melem->set_source_table('melem', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        // (none)

        // Define file annotations
        $melem->annotate_files('mod_melem', 'intro', null); // This file area hasn't itemid
		$melem->annotate_files('mod_melem', 'poster', null); // This file area hasn't itemid

        // Return the root element (melem), wrapped into standard activity structure
        return $this->prepare_activity_structure($melem);

    }
}
