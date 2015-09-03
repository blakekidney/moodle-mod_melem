<?php

/**
 * Elem module main user interface
 *
 * @package    mod_melem
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id       = optional_param('id', 0, PARAM_INT);        // Course module ID
$m        = optional_param('m', 0, PARAM_INT);         // melem instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($m) {  // Two ways to specify the module
    $melem = $DB->get_record('melem', array('id'=>$m), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('melem', $melem->id, $melem->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('melem', $id, 0, false, MUST_EXIST);
    $melem = $DB->get_record('melem', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/melem:view', $context);

$params = array(
    'context' => $context,
    'objectid' => $melem->id
);
$event = \mod_melem\event\course_module_viewed::create($params);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('melem', $melem);
$event->trigger();

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/melem/view.php', array('id' => $cm->id));

//*
if ($redirect) {
    // coming from course page or melem index page,
    // the redirection is needed for completion tracking and logging
    $fullurl = str_replace('&amp;', '&', url_get_full_url($melem, $cm, $course));

    if (!course_get_format($course)->has_view_page()) {
        // If course format does not have a view page, add redirection delay with a link to the edit page.
        // Otherwise teacher is redirected to the external melem without any possibility to edit activity or course settings.
        $editurl = null;
        if (has_capability('moodle/course:manageactivities', $context)) {
            $editurl = new moodle_url('/course/modedit.php', array('update' => $cm->id));
            $edittext = get_string('editthisactivity');
        } else if (has_capability('moodle/course:update', $context->get_course_context())) {
            $editurl = new moodle_url('/course/edit.php', array('id' => $course->id));
            $edittext = get_string('editcoursesettings');
        }
        if ($editurl) {
            redirect($fullurl, html_writer::link($editurl, $edittext)."<br/>".
                    get_string('pageshouldredirect'), 10);
        }
    }
    redirect($fullurl);
}
//*/

require_once($CFG->dirroot.'/mod/melem/locallib.php');
$config = melem_get_config();

//check the config to see if anything has been flagged as not allowed to modify settings
if(!$config->allow_maxwidth) $melem->maxwidth = $config->maxwidth;
if(!$config->allow_autoplay) $melem->autoplay = $config->autoplay;
if(!$config->allow_controls) $melem->controls = $config->controls;
if(!$config->allow_preload) $melem->preload = $config->preload;
if(!$config->allow_downloadlink) $melem->downloadlink = $config->downloadlink;

//pull the media element type
$type = ($melem->type == 'audio' ? 'audio' : 'video');
$jsopts = melem_create_js_options($type, $config);

//$mimetype = resourcelib_guess_url_mimetype($url->externalurl);
$PAGE->set_title($course->shortname.': '.$melem->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($melem);

//include the css and javascript files for media element
$PAGE->requires->css('/mod/melem/mediaelement/mediaelementplayer.min.css');
$PAGE->requires->jquery();
$PAGE->requires->js('/mod/melem/mediaelement/mediaelement-and-player.min.js');
$PAGE->requires->js_init_code("jQuery('#media-element-container').find('".$type."').mediaelementplayer(".$jsopts.");");

//display the page header
echo $OUTPUT->header();	
//display the page content header
echo $OUTPUT->heading(format_string($melem->name), 2);

//output the html5 video element
echo '<div id="media-element-container"';
if($melem->maxwidth) echo ' style="max-width:'.max($melem->maxwidth, 200).'px"';
echo '>';
echo '<'.$type;
if($melem->controls) echo ' controls="controls"';
if($melem->autoplay) echo ' autoplay="autoplay"';
if($type == 'video' && $config->allow_poster && !$melem->autoplay && $melem->posterurl) echo ' poster="'.$melem->posterurl.'"';
echo '>';
echo '<source src="'.$melem->src1.'">';
if(trim($melem->src2)) echo '<source src="'.$melem->src2.'">';
if(trim($melem->src3)) echo '<source src="'.$melem->src3.'">';
echo '</'.$type.'>';
if($melem->downloadlink) {
	echo '<p class="melemdownload" style="text-align:right; padding-top:7px">';
	echo '<span style="font-style:italic; font-size:85%; opacity:0.6">'.get_string('downloadtext', 'melem').'&nbsp;&nbsp;&nbsp;</span>';
	echo '<a class="btn btn-small" href="'.$melem->src1.'" download="'.basename($melem->src1).'">'.get_string('downloadlabel'.$type, 'melem').'</a>';
	echo '</p>';
}
echo '</div>';

//display the description below the video
if (trim(strip_tags($melem->intro))) {
	echo $OUTPUT->box_start('mod_introbox', 'melemintro');
	echo format_module_intro('melem', $melem, $cm->id);
	echo $OUTPUT->box_end();
}

//display the page footer
echo $OUTPUT->footer();

