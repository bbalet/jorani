<?php
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
 * 
 * @copyright  Copyright (c) 2014 - 2015 Benjamin BALET
 */

class MY_Loader extends CI_Loader {
  /**
   * Load a view that can be anywhere into the installation
   * @param string $folder path where we can find the extended views
   * @param string $view name of the extended view
   * @param array $vars data to be passed to the extended view
   * @param bool $return TRUE if we want to eval the view and to return it as a  string
   * @return string (optionnal) content of the parsed view
   */
  function ext_view($folder, $view, $vars = array(), $return = FALSE) {
    //We don't use $this->_ci_view_paths
    $this->_ci_view_paths = array_merge(array($folder . '/' => TRUE), $this->_ci_view_paths);
    return $this->_ci_load(array(
                '_ci_view' => $view,
                '_ci_vars' => $this->_ci_object_to_array($vars),
                '_ci_return' => $return
            ));
  }
}

?>