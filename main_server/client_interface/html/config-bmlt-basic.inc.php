<?php
/****************************************************************************************//**
*   This file is part of the Basic Meeting List Toolbox (BMLT).                             *
*                                                                                           *
*   Find out more at: http://bmlt.magshare.org                                              *
*                                                                                           *
*   BMLT is free software: you can redistribute it and/or modify                            *
*   it under the terms of the GNU General Public License as published by                    *
*   the Free Software Foundation, either version 3 of the License, or                       *
*   (at your option) any later version.                                                     *
*                                                                                           *
*   BMLT is distributed in the hope that it will be useful,                                 *
*   but WITHOUT ANY WARRANTY; without even the implied warranty of                          *
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                           *
*   GNU General Public License for more details.                                            *
*                                                                                           *
*   You should have received a copy of the GNU General Public License                       *
*   along with this code.  If not, see <http://www.gnu.org/licenses/>.                      *
********************************************************************************************/

$config_file_path = dirname ( __FILE__ ).'/../../server/config/get-config.php';
$url_path = 'http://'.$_SERVER['SERVER_NAME'].dirname ( $_SERVER['SCRIPT_NAME'] ).'/../..';
if ( file_exists ( $config_file_path ) )
    {
    include ( $config_file_path );
    }

require_once ( dirname ( __FILE__ ).'/../../server/shared/classes/comdef_utilityclasses.inc.php');
require_once ( dirname ( __FILE__ ).'/../../server/c_comdef_server.class.php');
require_once ( dirname ( __FILE__ ).'/../../local_server/db_connect.php');

DB_Connect_and_Upgrade ( );

$server = c_comdef_server::MakeServer();

if ( $server )
    {
    global $bmlt_localization;  ///< Use this to control the localization.
    $bmlt_localization = $server->GetLocalLang();
    
    if ( !isset ( $_SESSION ) )
        {
        session_start();
        }
    
    if ( (isset ( $_GET['admin_action'] ) && (($_GET['admin_action'] == 'logout'))) || (isset ( $_POST['admin_action'] ) && (($_POST['admin_action'] == 'login'))) )
        {
        // Belt and suspenders -nuke the stored login.
        $_SESSION[$admin_session_name] = null;
        unset ( $_SESSION[$admin_session_name] );

        if ( isset ( $_POST['admin_action'] ) && ($_POST['admin_action'] == 'login') )
            {
            $login = isset ( $_POST['c_comdef_admin_login'] ) ? $_POST['c_comdef_admin_login'] : null;

            // If this is a valid login, we'll get an encrypted password back.
            $enc_password = isset ( $_POST['c_comdef_admin_password'] ) ? $server->GetEncryptedPW ( $login, trim ( $_POST['c_comdef_admin_password'] ) ) : null;
            if ( null != $enc_password )
                {
                $_SESSION[$admin_session_name] = "$login\t$enc_password";
                }
            else
                {
                // Otherwise, we just check to make sure this is a kosher user.
                $user_obj = $server->GetCurrentUserObj();
                if ( !($user_obj instanceof c_comdef_user) || ($user_obj->GetUserLevel() == _USER_LEVEL_DISABLED) )
                    {
                    // Get the display strings.
                    $localized_strings = c_comdef_server::GetLocalStrings();
    
                    c_comdef_LogoutUser();

                    // If the login is invalid, we terminate the whole kit and kaboodle, and inform the user they are persona non grata.
                    die ( '<h2 class="c_comdef_not_auth_3">'.c_comdef_htmlspecialchars ( $localized_strings['comdef_server_admin_strings']['not_auth_3'] ).'</h2>'.c_comdef_LoginForm($server).'</body></html>' );
                    }
                }
            }

        // Make sure these get wiped and deleted.
        $_POST['admin_action'] = null;
        $_POST['c_comdef_admin_login'] = null;
        $_POST['c_comdef_admin_password'] = null;
        // Shouldn't have GET, but what the hell...
        $_GET['admin_action'] = null;
        $_GET['c_comdef_admin_login'] = null;
        $_GET['c_comdef_admin_password'] = null;
        // Belt and suspenders -we set them to naught, then unset them.
        unset ( $_POST['admin_action'] );
        unset ( $_POST['c_comdef_admin_login'] );
        unset ( $_POST['c_comdef_admin_password'] );
        unset ( $_GET['admin_action'] );
        unset ( $_GET['c_comdef_admin_login'] );
        unset ( $_GET['c_comdef_admin_password'] );
        }
    
    if ( isset ( $_SESSION[$admin_session_name] ) )
        {
        // Get the display strings.
        $localized_strings = c_comdef_server::GetLocalStrings();

        // We double-check, and see if the user is valid.
        $user_obj = $server->GetCurrentUserObj();
        
        if ( ($user_obj instanceof c_comdef_user) && ($user_obj->GetUserLevel() != _USER_LEVEL_DISABLED) )
            {
            $localized_strings = c_comdef_server::GetLocalStrings();
            global $bmlt_basic_configuration;       ///< These are used by the bmlt_basic class. Don't mess with them.
            global $bmlt_basic_configuration_index;

            $bmlt_basic_configuration = array();    ///< The configuration will be held in an array of associative arrays.
            $bmlt_basic_configuration_index = 0;

            $bmlt_basic_configuration[$bmlt_basic_configuration_index++] = array (
                'root_server'               =>  'http://'.$_SERVER['SERVER_NAME'].dirname ( $_SERVER['SCRIPT_NAME'] ).'/../..', 
                'map_center_latitude'       =>  floatval ( $localized_strings['search_spec_map_center']['latitude'] ),
                'map_center_longitude'      =>  floatval ( $localized_strings['search_spec_map_center']['longitude'] ),
                'map_zoom'                  =>  floatval ( $localized_strings['comdef_server_admin_strings']['meeting_editor_default_zoom'] ),
                'bmlt_initial_view'         =>  'text',
                'distance_units'            =>  $localized_strings['dist_units'],
                'bmlt_location_checked'     =>  1,
                'bmlt_location_services'    =>  1,
                'theme'                     =>  'BlueAndWhite',
                'grace_period'              =>  15,
                'time_offset'               =>  0,
                'id'                        =>  $bmlt_basic_configuration_index + 1
            );
            }
        else
            {
            die ( 'NOT AUTHORIZED' );
            }
        }
    }
?>