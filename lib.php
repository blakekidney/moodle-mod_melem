<?php

/**
 * Mandatory public API of melem module
 *
 * @package    mod_melem
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in melem module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function melem_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function melem_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function melem_reset_userdata($data) {
    return array();
}

/**
 * List of view style log actions
 * @return array
 */
function melem_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 * @return array
 */
function melem_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add melem instance.
 * @param object $data
 * @param object $mform
 * @return int new melem instance id
 */
function melem_add_instance($data, $mform) {
    global $CFG, $DB;
	require_once("$CFG->dirroot/mod/melem/locallib.php");
	
	$config = melem_get_config();
	
	if($config->allow_poster) {
		$data->posterurl = melem_get_file_url($data, $data->posterimage, 'poster');
	} else {
		$data->posterurl = '';
	}
	
    //ensure TEXT fields that have no default in database are filled
	$fields = array('src2' => '', 'src3' => '', 'downloadurl' => '');
	foreach($fields as $field => $default) {
		if(!isset($data->$field)) $data->$field = $default;
	}
	
	$data->timemodified = time();
    $data->id = $DB->insert_record('melem', $data);
	
    return $data->id;
}

/**
 * Update melem instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function melem_update_instance($data, $mform) {
    global $CFG, $DB;
	require_once("$CFG->dirroot/mod/melem/locallib.php");
	
	$config = melem_get_config();
	
	if($config->allow_poster) {
		$data->posterurl = melem_get_file_url($data, $data->posterimage, 'poster');
	} else {
		$data->posterurl = '';
	}
	
	//if any display values are not allowed, then set them to the configuration default
	if(!$config->allow_maxwidth) $data->maxwidth = $config->maxwidth;
	if(!$config->allow_autoplay) $data->autoplay = $config->autoplay;
	if(!$config->allow_controls) $data->controls = $config->controls;
	if(!$config->allow_preload) $data->preload = $config->preload;
	if(!$config->allow_downloadlink) $data->downloadlink = $config->downloadlink;	
	
    //ensure TEXT fields that have no default in database are filled
	$fields = array('src2' => '', 'src3' => '', 'downloadurl' => '');
	foreach($fields as $field => $default) {
		if(!isset($data->$field)) $data->$field = $default;
	}
	
	$data->timemodified = time();
    $data->id = $data->instance;

    $DB->update_record('melem', $data);

    return true;
}

/**
 * Delete melem instance.
 * @param int $id
 * @return bool true
 */
function melem_delete_instance($id) {
    global $DB;

    if (!$melem = $DB->get_record('melem', array('id'=>$id))) {
        return false;
    }

    // note: all context files are deleted automatically
    $DB->delete_records('melem', array('id'=>$melem->id));

    return true;
}
/**
 * Serves the melem files.
 *
 * @package  mod_melem
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function mod_melem_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;
	
	// Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }
	
	// Make sure the filearea is one of those used by the plugin.
	if(!in_array($filearea, array('poster'))) {
        return false;
    }
	
	// Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_course_login($course, true, $cm);
	
	// Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if(!has_capability('mod/melem:view', $context)) {
        return false;
    }

    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.
 
    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.
 
    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if(!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }
	
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_melem', $filearea, $itemid, $filepath, $filename);
    if(!$file) {
		return false; // The file does not exist.
    }
 
    // We can now send the file back to the browser  
    send_stored_file($file, null, 0, $forcedownload);

}

