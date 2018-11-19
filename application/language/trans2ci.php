<?php
/**
 * Utility script that converts a PO file to PHP array i18n files
 * @copyright  Copyright (c) 2014-2018 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.3.0
 */

require_once "../../vendor/autoload.php";
$target = "romanian";

$copyright = "<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2018 Benjamin BALET
 * @license    http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link       https://github.com/bbalet/jorani
 * @since      0.6.6
 * @author     Transifex users
 */\n\n";

//Load and parse the PO file
$fileHandler = new Sepia\FileHandler($target . DIRECTORY_SEPARATOR . $target . '.po');
$poParser = new Sepia\PoParser($fileHandler);
$entries  = $poParser->parse();
//$lenPO = count($messages[1]);
$lenPO = count($entries);

//Scan all translation files
$files = scandir($target);
foreach ($files as $file) {
    if (strpos($file, 'php') !== false) {
        $path = join_paths($target, $file);
        $ci18n = file_get_contents($path);

        //Analyse CI i18n files containing the translations (key/value)
        //$lang['calendar_individual_title'] = 'My calendar';
        $pattern = "\$lang\['(.*)'\] = '(.*)';$";
        $out = array(); //result to be inserted into the CI translation file
        preg_match_all($pattern, $ci18n, $out, PREG_PATTERN_ORDER);
        $lenI18N = count($out[0]);
        for ($jj = 0; $jj < $lenI18N; $jj++) {
            //for ($ii = 0; $ii < $lenPO; $ii++) {
            foreach ($entries as $entry) {
              //Example of parsed PO content:
              //$entries['Press this button to save']['msgstr'] = 'Pulsa este botón para guardar';
              //$translated = $entries[$msgid]['msgstr']

                //Handle multi line Msg Id
                $key = implode("", $entry['msgid']);
                $key = str_replace("'", "\\'", $key);

                //Replace the english entry by the translation, if any
                if ($out[2][$jj] != '') {
                    if (strcmp($key, $out[2][$jj]) == 0) {
                        $po2ci = str_replace("'", '\'', $entry['msgstr'][0]);
                        if ($entry['msgstr'][0] != '') {
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
