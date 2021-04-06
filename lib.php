<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Serve question type files
 *
 * @since      Moodle 2.0
 * @package    qtype_bsmultichoice
 * @copyright  Dongsheng Cai <dongsheng@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Checks file access for multiple choice questions.
 *
 * @package  qtype_bsmultichoice
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 */
function qtype_bsmultichoice_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    if($filearea == "infographicdata"){
        // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
        if ($context->contextlevel != CONTEXT_COURSE) {
            return false;
        }
        $itemid = $args[2];
        $filename = array_pop($args);

        $filepath = '/';
        $fs = get_file_storage();
        $file = $fs->get_file($context->id, 'qtype_bsmultichoice', $filearea, $itemid, $filepath, $filename);
        if (!$file) {
            return false;
        }
        send_stored_file($file, 0, 0, $forcedownload, $options);
    }
    else{
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');
        question_pluginfile($course, $context, 'qtype_bsmultichoice', $filearea, $args, $forcedownload, $options);
    }
}
