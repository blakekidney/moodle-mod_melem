<?php

/**
 * Media Element module admin settings
 *
 * @package    mod_melem
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if($ADMIN->fulltree) {
	require_once($CFG->dirroot.'/mod/melem/locallib.php');
	require_once($CFG->dirroot.'/mod/melem/settingslib.php');
	
	$defaults = melem_config_defaults();	
	$yesno_options = array(1 => 'Yes', 0 => 'No');
	
	$settings->add(new admin_setting_confightml('melemmoddesc', get_string('config_desc', 'melem')));
	
	//--- default settings -----------------------------------------------------------------------------------
	$settings->add(new admin_setting_heading('melemmodheaderdefault', get_string('config_header_default', 'melem'), get_string('config_header_default_desc', 'melem')));
	
	$settings->add(new admin_setting_configtext('melem/maxwidth',
        get_string('maxwidth', 'melem'), get_string('maxwidth_help', 'melem'), $defaults['maxwidth'], PARAM_INT, 7));
	
	$settings->add(new admin_setting_configselect('melem/autoplay',
        get_string('autoplay', 'melem'), get_string('autoplay_help', 'melem'), $defaults['autoplay'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/controls',
        get_string('controls', 'melem'), get_string('controls_help', 'melem'), $defaults['controls'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/preload',
        get_string('preload', 'melem'), get_string('preload_help', 'melem'), $defaults['preload'], melem_get_preload_options()));
	
	$settings->add(new admin_setting_configselect('melem/downloadlink',
        get_string('downloadlink', 'melem'), get_string('downloadlink_help', 'melem'), $defaults['downloadlink'], $yesno_options));
	
	//--- show settings -----------------------------------------------------------------------------------
	$settings->add(new admin_setting_heading('melemmodheadershow', get_string('config_header_show', 'melem'), get_string('config_header_show_desc', 'melem')));
	
	$settings->add(new admin_setting_configselect('melem/allow_maxwidth',
        get_string('config_allow_maxwidth', 'melem'), get_string('config_allow_maxwidth_desc', 'melem'), $defaults['allow_maxwidth'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/allow_autoplay',
        get_string('config_allow_autoplay', 'melem'), get_string('config_allow_autoplay_desc', 'melem'), $defaults['allow_autoplay'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/allow_controls',
        get_string('config_allow_controls', 'melem'), get_string('config_allow_controls_desc', 'melem'), $defaults['allow_controls'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/allow_preload',
        get_string('config_allow_preload', 'melem'), get_string('config_allow_preload_desc', 'melem'), $defaults['allow_preload'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/allow_downloadlink',
        get_string('config_allow_downloadlink', 'melem'), get_string('config_allow_downloadlink_desc', 'melem'), $defaults['allow_downloadlink'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/allow_poster',
        get_string('config_allow_poster', 'melem'), get_string('config_allow_poster_desc', 'melem'), $defaults['allow_poster'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/allow_onesource',
        get_string('config_allow_onesource', 'melem'), get_string('config_allow_onesource_desc', 'melem'), $defaults['allow_onesource'], $yesno_options));
	
	//--- default settings -----------------------------------------------------------------------------------
	$settings->add(new admin_setting_heading('melemmodheaderadvanced', get_string('config_header_advanced', 'melem'), get_string('config_header_advanced_desc', 'melem')));
	
	$volume_options = array('1' => '100%', '0.9' => '90%', '0.8' => '80%', '0.7' => '70%', '0.6' => '60%', '0.5' => '50%', '0.4' => '40%', '0.3' => '30%', '0.2' => '20%', '0.1' => '10%');
	$settings->add(new admin_setting_configselect('melem/startvolume',
        get_string('config_startvolume', 'melem'), get_string('config_startvolume_desc', 'melem'), $defaults['startvolume'], $volume_options));
	
	$settings->add(new admin_setting_configtextarea('melem/controlbar',
        get_string('config_controlbar', 'melem'), get_string('config_controlbar_desc', 'melem'), null, PARAM_TEXT, 60, 2));
	
	$settings->add(new admin_setting_configselect('melem/alwaysshowcontrols',
        get_string('config_alwaysshowcontrols', 'melem'), get_string('config_alwaysshowcontrols_desc', 'melem'), $defaults['alwaysshowcontrols'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/ipadusenativecontrols',
        get_string('config_ipadusenativecontrols', 'melem'), get_string('config_ipadusenativecontrols_desc', 'melem'), $defaults['ipadusenativecontrols'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/iphoneusenativecontrols',
        get_string('config_iphoneusenativecontrols', 'melem'), get_string('config_iphoneusenativecontrols_desc', 'melem'), $defaults['iphoneusenativecontrols'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/androidusenativecontrols',
        get_string('config_androidusenativecontrols', 'melem'), get_string('config_androidusenativecontrols_desc', 'melem'), $defaults['androidusenativecontrols'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/alwaysshowhours',
        get_string('config_alwaysshowhours', 'melem'), get_string('config_alwaysshowhours_desc', 'melem'), $defaults['alwaysshowhours'], $yesno_options));
	
	$settings->add(new admin_setting_configselect('melem/showtimecodeframecount',
        get_string('config_showtimecodeframecount', 'melem'), get_string('config_showtimecodeframecount_desc', 'melem'), $defaults['showtimecodeframecount'], $yesno_options));
	
	$settings->add(new admin_setting_configtext('melem/framespersecond',
        get_string('config_framespersecond', 'melem'), get_string('config_framespersecond_desc', 'melem'), $defaults['framespersecond'], PARAM_INT));
}