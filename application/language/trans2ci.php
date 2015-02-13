<?php

/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * lms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 */

//Utility script that converts a PO file to PHP array i18n files
require("POParser.php");
$target = "spanish";

$copyright = "<?php
/*
 * This file is part of Jorani.
 *
 * Jorani is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jorani is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jorani.  If not, see <http://www.gnu.org/licenses/>.
 */\n\n";

//Load and parse the PO file
$parser = new POParser;
$messages = $parser->parse($target . '.po');
$lenPO = count($messages[1]);

//Scan all translation files
$files = scandir($target);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $path = join_paths($target, $file);
        $ci18n = file_get_contents($path);

        //Analyse CI i18n files containing the translations (key/value)
        //$lang['calendar_individual_title'] = 'My calendar';
        $pattern = "\$lang\['(.*)'\] = '(.*)';$";
        $out = array();
        preg_match_all($pattern, $ci18n, $out, PREG_PATTERN_ORDER);
        $lenI18N = count($out[0]);
        for ($jj = 0; $jj < $lenI18N; $jj++) {
            for ($ii = 0; $ii < $lenPO; $ii++) {
                $po2ci = str_replace('\"', '"', $messages[1][$ii]['msgid']);
                if ($out[2][$jj] != '') {
                    $po2ci = str_replace('\"', '"', $messages[1][$ii]['msgstr']);
                    $po2ci = str_replace("'", '\'', $po2ci);
                    if (strcmp($po2ci, $out[2][$jj]) == 0) {
                        if ($messages[1][$ii]['msgstr'] != '') {
                            $out[2][$jj] = $po2ci;
                        }
                    }
                }
            }
        }

        //Overwrite CI i18n with the target translation strings
        $output = $copyright;
        for ($jj = 0; $jj < $lenI18N; $jj++) {
            $output .= '$lang[\'' . $out[1][$jj] . "'] = '" . $out[2][$jj] . "';" . PHP_EOL;
        }
        file_put_contents($path, $output);
    }
}

//Internal utility function to join paths	
function join_paths() {
    $paths = array();
    foreach (func_get_args() as $arg) {
        if ($arg !== '') {
            $paths[] = $arg;
        }
    }
    return preg_replace('#/+#', '/', join(DIRECTORY_SEPARATOR, $paths));
}
