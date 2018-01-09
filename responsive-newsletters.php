<?php
/*
 * Plugin Name: Responsive Newsletters
 * Plugin URI: http://www.nevma.gr
 * Description: Create responsive Newsletters from within your WordPress site.
 * Version: 1.0.0
 * Author: Nevma
 * Author URI: http://www.nevma.gr
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if( ! defined( 'ABSPATH' ) ) { exit; }// Exit if accessed directly

require_once 'init.php';

register_activation_hook( __FILE__, 'mnltr_cpt_flush_rewrite_rules' );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

?>