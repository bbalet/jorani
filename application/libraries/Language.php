<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * This file is part of lms.
 *
 * lms is free software: you can redistribute it and/or modify
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
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
 */

class Language {
    /**
     * Default constructor
     */
    public function __construct() {
    }

    /**
     * Explodes a string containing a comma separated list of language codes into an associative array
     * You can pass the config object $this->config->item('languages') as a parameter
     * @param type $languages_list comma separated list of language codes
     * @return array associative array (language code => english language name)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function languages($languages_list) {
        $languages = array();
        $lang_codes = explode(",", $languages_list);
        foreach($lang_codes as $lang_code) {
            $languages[$lang_code] =  $this->code2language($lang_code) ;
        }
        return $languages;
    }
 
    /**
     * Explodes a string containing a comma separated list of language codes into an associative array
     * You can pass the config object $this->config->item('languages') as a parameter
     * @param type $languages_list comma separated list of language codes
     * @return array associative array (language code => language native name)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function nativelanguages($languages_list) {
        $languages = array();
        $lang_codes = explode(",", $languages_list);
        foreach($lang_codes as $lang_code) {
            $languages[$lang_code] =  $this->code2nativelanguage($lang_code) ;
        }
        return $languages;
    }
    
    /**
     * Convert a two characters language code to the english language name
     * @param string $code ISO 639-1 language code
     * @return string english language name
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function code2language($code) {
        switch ($code) {
            //User management
            case 'en' : return 'english'; break;
            case 'fr' : return 'french'; break;
            case 'kh' : return 'khmer'; break;
            default: return 'english'; break;
        }
    }

    /**
     * Returns the ISO 639-1 language code of an english language name
     * @param string $code english language name
     * @return string ISO 639-1 language code
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function language2code($language) {
        switch ($language) {
            //User management
            case 'english' : return 'en'; break;
            case 'french' : return 'fr'; break;
            case 'khmer' : return 'kh'; break;
            default: return 'en'; break;
        }
    }
    
    /**
     * Convert a two characters language code to the language native name
     * @param string $code ISO 639-1 language code
     * @return string language native name
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function code2nativelanguage($code) {
        switch ($code) {
            //User management
            case 'en' : return 'english'; break;
            case 'fr' : return 'français'; break;
            case 'kh' : return 'ភាសាខ្មែរ'; break;
            default: return 'english'; break;
        }
    }

    /**
     * Returns the ISO 639-1 language code of a language native name
     * @param string language native name
     * @return string $code ISO 639-1 language code
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function nativelanguage2code($language) {
        switch ($language) {
            //User management
            case 'english' : return 'en'; break;
            case 'français' : return 'fr'; break;
            case 'ភាសាខ្មែរ' : return 'kh'; break;
            default: return 'en'; break;
        }
    }
}
