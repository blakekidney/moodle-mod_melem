<?php

/**
 * Defines backup_url_activity_task class
 *
 * @package     mod_melem
 * @category    backup
 * @copyright  	2014 Blake Kidney
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/melem/backup/moodle2/backup_melem_stepslib.php');

/**
 * Provides all the settings and steps to perform one complete backup of the activity
 */
class backup_melem_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the melem.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_melem_activity_structure_step('melem_structure', 'melem.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot.'/mod/melem','#');

        //Access a list of all links in a course
        $pattern = '#('.$base.'/index\.php\?id=)([0-9]+)#';
        $replacement = '$@URLINDEX*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        //Access the link supplying a course module id
        $pattern = '#('.$base.'/view\.php\?id=)([0-9]+)#';
        $replacement = '$@URLVIEWBYID*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        //Access the link supplying an instance id
        $pattern = '#('.$base.'/view\.php\?u=)([0-9]+)#';
        $replacement = '$@URLVIEWBYU*$2@$';
        $content = preg_replace($pattern, $replacement, $content);

        return $content;
    }
}
