<?php

/**
 * Private melem module utility functions
 *
 * @package    mod_melem
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Returns the default values for the configuration
 * @return array
 */
function melem_config_defaults() {
	return array(
		'maxwidth'       			=> 640,
		'autoplay'          		=> 1,
		'controls'     				=> 1,
		'preload'            		=> 'auto',
		'downloadlink'  			=> 0,
		'allow_maxwidth'          	=> 1,
		'allow_autoplay' 			=> 1,
		'allow_controls'   			=> 1,
		'allow_preload'         	=> 1,
		'allow_downloadlink'        => 1,
		'allow_poster'       		=> 1,
		'allow_onesource'     		=> 1,
		'startvolume'   			=> 1,
		'controlbar'   				=> 'playpause,current,progress,duration,volume,fullscreen',
		'alwaysshowcontrols' 		=> 0,
		'ipadusenativecontrols'     => 0,
		'iphoneusenativecontrols'   => 0,
		'androidusenativecontrols'  => 0,
		'alwaysshowhours'    		=> 0,
		'showtimecodeframecount'    => 0,
		'framespersecond'    		=> 25,
	);
}
/**
 * Returns configuration with default values set.
 * @return object
 */
$melem_config_cache = false;  //helps to reduce overhead when calling this from multiple places
function melem_get_config() {
	global $melem_config_cache;
	if($melem_config_cache !== false) return $melem_config_cache;
	$melem_config_cache = get_config('melem');
	$defaults = melem_config_defaults();
	foreach($defaults as $name => $value) {
		if(!isset($melem_config_cache->$name)) {
			$melem_config_cache->$name = $defaults[$name];			
		}
	}	
	return $melem_config_cache;	
}

/**
 * Resets the cache variable for the configuration
 * @return object
 */
function melem_clear_config_cache() {
	$melem_config_cache = false;
}

/**
 * Formats the control bar string as an array for Javacript
 * @return array
 */
function melem_format_controls($controls) {
	return "['".implode("','", preg_split("/['\"]?\s*,\s*['\"]?/", trim($controls, " \t\n\r\0\x0B\"'"), -1, PREG_SPLIT_NO_EMPTY))."']"; 
}

/**
 * Creates the js for the media element options
 * @return object
 */
function melem_create_js_options($type, $config) {
	
	$jsopts = "{ ";
	$jsopts .= $type."Width: '100%', ".$type."Height: '100%'";
	$jsopts .= ", startVolume: ".$config->startvolume;
	$jsopts .= ", features: ".melem_format_controls($config->controlbar);
	if($config->alwaysshowcontrols) $jsopts .= ", alwaysShowControls: true";
	if($config->ipadusenativecontrols) $jsopts .= ", iPadUseNativeControls: true";
	if($config->iphoneusenativecontrols) $jsopts .= ", iPhoneUseNativeControls: true";
	if($config->androidusenativecontrols) $jsopts .= ", AndroidUseNativeControls: true";
	if($config->alwaysshowhours) $jsopts .= ", alwaysShowHours: true";
	if($config->showtimecodeframecount) $jsopts .= ", showTimecodeFrameCount: true, framesPerSecond: ".$config->framespersecond;
	$jsopts .= " }";
	
	return $jsopts;
}


/**
 * Returns the default values for the configuration
 * @return array
 */
function melem_get_preload_options() {
	return array('auto' => 'auto', 'none' => 'none', 'metadata' => 'metadata');
}

/**
 * Print media element introduction.
 * @param object $melem
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function melem_print_intro($melem, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($melem->displayoptions) ? array() : unserialize($melem->displayoptions);   

    if ($ignoresettings || !empty($options['printintro'])) {
        $gotintro = trim(strip_tags($melem->intro));
        if ($gotintro) {
            echo $OUTPUT->box_start('mod_introbox', 'melemintro');
            if ($gotintro) {
                echo format_module_intro('melem', $melem, $cm->id);
            }
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Gets the url to the uploaded file.
 * @param object $data data from the mform
 * @param object $filearea 
 * @return string
 */
function melem_get_file_url($data, $draftitemid, $filearea) {
    
	//if there is no draftitemid, then return blank
	if(!$draftitemid) return '';
	
	$fs = get_file_storage();
	$cmid = $data->coursemodule;
	$context = context_module::instance($cmid);
	$component = 'mod_melem';
		
	/*
	file_save_draft_area_files($draftitemid, $contextid, $component, $filearea, $itemid, array $options=null, $text=null, $forcehttps=false)
	@param int $draftitemid the id of the draft area to use. Normally obtained
	      from file_get_submitted_draft_itemid('elementname') or similar.
	@param int $contextid This parameter and the next two identify the file area to save to.
	@param string $component
	@param string $filearea indentifies the file area.
	@param int $itemid helps identifies the file area.
	@param array $options area options (subdirs=>false, maxfiles=-1, maxbytes=0)
	@param string $text some html content that needs to have embedded links rewritten
	     to the @@PLUGINFILE@@ form for saving in the database.
	@param bool $forcehttps force https urls.
	*/	
	
	file_save_draft_area_files(
		$draftitemid, 
		$context->id, 
		$component, 
		$filearea,
		0, 
		array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1)
	);		
	
	$files = $fs->get_area_files($context->id, $component, $filearea, 0, 'sortorder', false);
	if(empty($files)) return '';
	$file = reset($files);
	unset($files);
	/*
	MOODLE URL CONVENTION
	$url = $CFG->wwwroot/pluginfile.php/$contextid/$component/$filearea/arbitrary/extra/infomation.ext
	$url = $CFG->wwwroot/pluginfile.php/$forumcontextid/mod_forum/post/$postid/image.jpg
	http://localhost/m27/pluginfile.php/26/mod_resource/content/0/icon.png
	http://localhost/m27/pluginfile.php/25/mod_elem/poster/0/IntegrateLogo2.png
	*/
	$url = moodle_url::make_pluginfile_url(
		$file->get_contextid(), 
		$file->get_component(), 
		$file->get_filearea(), 
		$file->get_itemid(), 
		$file->get_filepath(), 
		$file->get_filename()
	);

    return $url;
}

