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
 * @package    qtype
 * @subpackage bsmultichoice
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * BSMultichoice question type conversion handler
 */
class moodle1_qtype_bsmultichoice_handler extends moodle1_qtype_handler {

    /**
     * @return array
     */
    public function get_question_subpaths() {
        return array(
            'ANSWERS/ANSWER',
            'BSMULTICHOICE',
        );
    }

    /**
     * Appends the bsmultichoice specific information to the question
     */
    public function process_question(array $data, array $raw) {

        // Convert and write the answers first.
        if (isset($data['answers'])) {
            $this->write_answers($data['answers'], $this->pluginname);
        }

        // Convert and write the bsmultichoice.
        if (!isset($data['bsmultichoice'])) {
            // This should never happen, but it can do if the 1.9 site contained
            // corrupt data.
            $data['bsmultichoice'] = array(array(
                'single'                         => 1,
                'shuffleanswers'                 => 1,
                'correctfeedback'                => '',
                'correctfeedbackformat'          => FORMAT_HTML,
                'partiallycorrectfeedback'       => '',
                'partiallycorrectfeedbackformat' => FORMAT_HTML,
                'incorrectfeedback'              => '',
                'incorrectfeedbackformat'        => FORMAT_HTML,
                'answernumbering'                => 'abc',
                'showstandardinstruction'        => 0
            ));
        }
        $this->write_bsmultichoice($data['bsmultichoice'], $data['oldquestiontextformat'], $data['id']);
    }

    /**
     * Converts the bsmultichoice info and writes it into the question.xml
     *
     * @param array $bsmultichoices the grouped structure
     * @param int $oldquestiontextformat - {@see moodle1_question_bank_handler::process_question()}
     * @param int $questionid question id
     */
    protected function write_bsmultichoice(array $bsmultichoices, $oldquestiontextformat, $questionid) {
        global $CFG;

        // The grouped array is supposed to have just one element - let us use foreach anyway
        // just to be sure we do not loose anything.
        foreach ($bsmultichoices as $bsmultichoice) {
            // Append an artificial 'id' attribute (is not included in moodle.xml).
            $bsmultichoice['id'] = $this->converter->get_nextid();

            // Replay the upgrade step 2009021801.
            $bsmultichoice['correctfeedbackformat']               = 0;
            $bsmultichoice['partiallycorrectfeedbackformat']      = 0;
            $bsmultichoice['incorrectfeedbackformat']             = 0;

            if ($CFG->texteditors !== 'textarea' and $oldquestiontextformat == FORMAT_MOODLE) {
                $bsmultichoice['correctfeedback']                 = text_to_html($bsmultichoice['correctfeedback'], false, false, true);
                $bsmultichoice['correctfeedbackformat']           = FORMAT_HTML;
                $bsmultichoice['partiallycorrectfeedback']        = text_to_html($bsmultichoice['partiallycorrectfeedback'], false, false, true);
                $bsmultichoice['partiallycorrectfeedbackformat']  = FORMAT_HTML;
                $bsmultichoice['incorrectfeedback']               = text_to_html($bsmultichoice['incorrectfeedback'], false, false, true);
                $bsmultichoice['incorrectfeedbackformat']         = FORMAT_HTML;
            } else {
                $bsmultichoice['correctfeedbackformat']           = $oldquestiontextformat;
                $bsmultichoice['partiallycorrectfeedbackformat']  = $oldquestiontextformat;
                $bsmultichoice['incorrectfeedbackformat']         = $oldquestiontextformat;
            }

            $bsmultichoice['correctfeedback'] = $this->migrate_files(
                    $bsmultichoice['correctfeedback'], 'question', 'correctfeedback', $questionid);
            $bsmultichoice['partiallycorrectfeedback'] = $this->migrate_files(
                    $bsmultichoice['partiallycorrectfeedback'], 'question', 'partiallycorrectfeedback', $questionid);
            $bsmultichoice['incorrectfeedback'] = $this->migrate_files(
                    $bsmultichoice['incorrectfeedback'], 'question', 'incorrectfeedback', $questionid);

            $this->write_xml('bsmultichoice', $bsmultichoice, array('/bsmultichoice/id'));
        }
    }
}
