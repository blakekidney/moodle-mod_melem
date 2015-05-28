<?php

/**
 * Definition of log events
 *
 * @package    mod_melem
 * @category   log
 * @copyright  2014 Blake Kidney
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'melem', 'action'=>'view', 'mtable'=>'melem', 'field'=>'name'),
    array('module'=>'melem', 'action'=>'view all', 'mtable'=>'melem', 'field'=>'name'),
    array('module'=>'melem', 'action'=>'update', 'mtable'=>'melem', 'field'=>'name'),
    array('module'=>'melem', 'action'=>'add', 'mtable'=>'melem', 'field'=>'name'),
);