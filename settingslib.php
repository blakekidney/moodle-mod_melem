<?php

/**
 * Extends admin settings.
 *
 * @package    mod_melem
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Creates an administration setting that allows for displaying a block of html
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_confightml extends admin_setting {

    /**
     * @param string $name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct($name, $html) {
        parent::__construct($name, '', $html, '', PARAM_RAW);
    }
	
	/**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return '';
    }

    public function write_setting($data) {
        return false;
    }
	
    /**
     * Returns an XHTML string for the hidden field
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        return '<div id="'. $this->get_id() .'"/>'.$this->description.'</div>';
    }
}