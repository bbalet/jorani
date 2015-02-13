<?php
/**
 * Copyright (C) 2008, Iulian Ilea (http://iulian.net), all rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class POParser
{
    private $_filename;

    /**
     * Format of a msgid entry:
     * array(
     *      'references'   => array(),		// each file on a new line
     *      'translator-comments'   => '',
     *      'extracted-comments'    => '',
     *      'flags'        => array(
     *          'fuzzy'
     *          ...
     *      ),
     *      'previous-msgctxt'  => '',
     *      'previous-msgid'    => '',
     *      'msgctxt'       => '',
     *      'msgid'         => '',
     *
     *      // when no plural forms
     *      'msgstr'        => '',
     *
     *      // when plural forms
     *      'msgid_pural'   => '',
     *      'msgstr'        => array(
     *          0   => '',                  // singular
     *          1   => '',                  // 1st plural form
     *          2   => '',                  // 2nd plural form
     *          ...
     *          n   => ''                   // nth plural form
     *      )
     * )
     *
     * @see http://www.gnu.org/software/gettext/manual/gettext.html#PO-Files
     */

    protected function _dequote($str)
    {
        return substr($str, 1, -1);
    }

    public function parse($filename)
    {
        // basic file verification
        if (!is_file($filename)) {
            throw new Exception('The specified file does not exist.');
        }
        if (substr($filename, strrpos($filename, '.')) !== '.po') {
            throw new Exception('The specified file is not a PO file.');
        }

        $lines = file($filename, FILE_IGNORE_NEW_LINES);

        // on the first two lines I'm expecting msgid respectively msgstr,
        // both containing empty strings
        $entries = array(
//            array(
//                'msgid'     => '',
//                'msgstr'    => array('')
//            )
        );

        // parsing headers; stop at the first empty line
        $headers = array(
            'Project-Id-Version'            => '',
            'Report-Msgid-Bugs-To'          => '',
            'POT-Creation-Date'             => '',
            'PO-Revision-Date'              => '',
            'Last-Translator'               => '',
            'Language-Team'                 => '',
            'Content-Type'                  => '',
            'Content-Transfer-Encoding'     => '',
            'Plural-Forms'                  => '',
        );
        $i = 2;
        while ($line = $lines[$i++]) {
            $line = $this->_dequote($line);
            $colonIndex = strpos($line, ':');
            if ($colonIndex === false) {
                continue;
            }
            $headerName = substr($line, 0, $colonIndex);
            if (!isset($headers[$headerName])) {
                continue;
            }
            // skip the white space after the colon and remove the \n at the end
            $headers[$headerName] = substr($line, $colonIndex + 1, -2);
        }

        $entry = array();
        for ($n = count($lines); $i < $n; $i++) {
            $line = $lines[$i];
            if ($line === '') {
                $entries[] = $entry;
                $entry = array();
                continue;
            }
            if ($line[0] == '#') {
                $comment = substr($line, 3);
                switch ($line[1]) {
                    // translator comments
                    case ' ': {
                        if (!isset($entry['translator-comments'])) {
                            $entry['translator-comments'] = $comment;
                        }
                        else {
                            $entry['translator-comments'] .= "\n" . $comment;
                        }
                        break;
                    }
                    // extracted comments
                    case '.': {
                        if (!isset($entry['extracted-comments'])) {
                            $entry['extracted-comments'] = $comment;
                        }
                        else {
                            $entry['extracted-comments'] .= "\n" . $comment;
                        }
                        break;
                    }
                    // reference
                    case ':': {
                        if (!isset($entry['references'])) {
                            $entry['references'] = array();
                        }
                        $entry['references'][] = $comment;
                        break;
                    }
                    // flag
                    case ',': {
                        if (!isset($entry['flags'])) {
                            $entry['flags'] = array();
                        }
                        $entry['flags'][] = $comment;
                        break;
                    }
                    // previous msgid, msgctxt
                    case '|': {
                        // msgi[d]
                        if ($comment[4] == 'd') {
                            $entry['previous-msgid'] = $this->_dequote(substr($comment, 6));
                        }
                        // msgc[t]xt
                        else {
                            $entry['previous-msgctxt'] = $this->_dequote(substr($comment, 8));
                        }
                        break;
                    }
                }
            }
            else if (strpos($line, 'msgid') === 0) {
                if ($line[5] === ' ') {
                    $entry['msgid'] = $this->_dequote(substr($line, 6));
                }
                // msgid[_]plural
                else {
                    $entry['msgid_plural'] = $this->_dequote(substr($line, 13));
                }
            }
            else if (strpos($line, 'msgstr') === 0) {
                // no plural forms
                if ($line[6] === ' ') {
                    $entry['msgstr'] = $this->_dequote(substr($line, 7));
                }
                // plural forms
                else {
                    if (!isset($entry['msgstr'])) {
                        $entry['msgstr'] = array();
                    }
                    $entry['msgstr'][] = $this->_dequote(substr($line, strpos($line, ' ') + 1));
                }
            }
            else if ($line[0] === '"' && isset($entry['msgstr'])) {
                $line = "\n" . preg_replace('/([^\\\\])\\\\n$/', "\$1\n", $this->_dequote($line));
                if (!is_array($entry['msgstr'])) {
                    $entry['msgstr'] .= $line;
                }
                else {
                    $entry['msgstr'][count($entry['msgstr']) - 1] .= $line;
                }
            }
        }
        return array($headers, $entries);
    }
}