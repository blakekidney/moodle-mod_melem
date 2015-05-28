<?php

/**
 * @package    	mod_melem
 * @subpackage 	backup-moodle2
 * @copyright  	2014 Blake Kidney
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_melem_activity_task
 */

/**
 * Structure step to restore one melem activity
 */
class restore_melem_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('melem', '/activity/melem');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_melem($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the melem record
        $newitemid = $DB->insert_record('melem', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add melem related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_melem', 'intro', null);
		$this->add_related_files('mod_melem', 'posterurl', null);
    }
}
