<?php
/*
Plugin Name: Lowa
Plugin URI: www.memyselfandhoney.xyz
Description: Simple Restaurant System Plugin for one of my courses
Version: Alpha
Author: eralpsahin
Author URI: www.memyselfandhoney.xyz
License: GPL2
*/

//Exit if accessed directly
if(!defined('ABSPATH'))
  exit;

register_activation_hook( __FILE__, 'lowa_db' );

function lowa_db() 
{
  global $wpdb;
  $menuTable = $wpdb->prefix.'lowa_menu';
  $ingredientTable = $wpdb->prefix.'lowa_ingredient';
  $containTable = $wpdb->prefix.'lowa_contain';
  $tableTable = $wpdb->prefix.'lowa_table';
  $groupTable = $wpdb->prefix.'lowa_group';
  $orderTable = $wpdb->prefix.'lowa_order';
  $charset_collate = $wpdb->get_charset_collate();
  
 
  $sql = "CREATE TABLE IF NOT EXISTS $menuTable
		(
			m_price REAL NOT NULL,
			m_id VARCHAR(200),
			m_stock int DEFAULT 100 NOT NULL,
			m_name VARCHAR(200) NOT NULL,
			PRIMARY KEY(m_id)
		)$charset_collate;";
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  $wpdb->query($sql);

  
  $sql = "CREATE TABLE IF NOT EXISTS $ingredientTable
      (
        i_name VARCHAR(200),
        PRIMARY KEY(i_name)
      )$charset_collate;";
  $wpdb->query($sql);

 
 $sql = "CREATE TABLE IF NOT EXISTS $containTable
      (
        m_id VARCHAR(200),
         i_name VARCHAR(200),
         PRIMARY KEY(m_id),
         FOREIGN KEY(m_id) REFERENCES $menuTable(m_id),
         FOREIGN KEY(i_name) REFERENCES $ingredientTable(i_name)
      )$charset_collate;";
  $wpdb->query($sql);

 
  $sql = "CREATE TABLE IF NOT EXISTS $tableTable
      (
        t_id int AUTO_INCREMENT,
       	t_capacity int NOT NULL,
        PRIMARY KEY(t_id)
      )$charset_collate;";
  $wpdb->query($sql);


 
 $sql = "CREATE TABLE IF NOT EXISTS $groupTable
      (
        g_id int AUTO_INCREMENT,
        g_people int NOT NULL,
        g_bill REAL,
        t_id int, 
        PRIMARY KEY(g_id),
        FOREIGN KEY(t_id) REFERENCES $tableTable(t_id)
      )$charset_collate;";
  $wpdb->query($sql);

  $sql = "CREATE TABLE IF NOT EXISTS $orderTable
      (
        g_id int,
        m_id VARCHAR(200),
        o_num int,
        PRIMARY KEY(g_id,m_id),
        FOREIGN KEY(m_id) REFERENCES $menuTable(m_id),
        FOREIGN KEY(g_id) REFERENCES $groupTable(g_id)
      )$charset_collate;";
  $wpdb->query($sql);
}

register_uninstall_hook( __FILE__, 'lowa_uninstall' );

function lowa_uninstall()
{
  global $wpdb; //required global declaration of WP variable
  $menuTable = $wpdb->prefix.'lowa_menu';
  $ingredientTable = $wpdb->prefix.'lowa_ingredient';
  $containTable = $wpdb->prefix.'lowa_contain';
  $tableTable = $wpdb->prefix.'lowa_table';
  $groupTable = $wpdb->prefix.'lowa_group';
  $orderTable = $wpdb->prefix.'lowa_order';
  $sql = "DROP TABLE ". $menuTable;
  $wpdb->query($sql);
  $sql = "DROP TABLE ". $ingredientTable;
  $wpdb->query($sql);
  $sql = "DROP TABLE ". $containTable;
  $wpdb->query($sql);
  $sql = "DROP TABLE ". $tableTable;
  $wpdb->query($sql);
  $sql = "DROP TABLE ". $groupTable;
  $wpdb->query($sql);
  $sql = "DROP TABLE ". $orderTable;
  $wpdb->query($sql);
}


//TODO shortcode for inserting new meal to the menu
function lowa_addmenu()
{
	ob_start();
	if(is_user_logged_in())
	{
		
		echo '<form id="add-menu-form" method="post" action="lowa.php"';
	   	echo 'Input format examples are given in input placeholder<br>';
	    echo 'Menu Id:*<br>';
 		 echo '<input type="text" id="menu_id" placeholder="C1 or NB3 or Y2" required><br>';
  echo' Menu Price:*<br>';
  echo '<input type="text" id="menu_price" placeholder="15.25 or 16" required><br><br>';
  echo' Menu Name:*<br>';
  echo '<input type="text" id="menu_name" placeholder="Lamb Meat Soup or Spicy Cucumber Salad" required><br><br>';
  echo' Menu Stock:<br>';
  echo '<input type="number" id="menu_stock" min="1" max ="200" value="100"><br><br>';
  echo '<input type="submit" value="Add Menu to Database">';
	    
	    echo '</form>';
	}
	return ob_get_clean();
}
function lowa_insertmenu()
{
	if ( isset( $_POST['menu_id'] ) && isset( $_POST['menu_price'] ) && isset( $_POST['menu_name'] ) && isset( $_POST['menu_stock'] ) && wp_verify_nonce($_POST['lowa_nonce'], 'lowa-nonce') )
  {
	global $wpdb; //required global declaration of WP variable
  	$menuTable = $wpdb->prefix.'lowa_menu';


  	echo 'finished';
  }
  else
  {
    echo 'not entered';
  }
  	die();
}

function lowa_scripts() {
    wp_enqueue_script('lowa-script', plugins_url() . '/lowa-plugin/lowa.js', array( 'jquery' ) ); 
     wp_localize_script( 'lowa-script', 'lowadata', 
       array('ajaxurl' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('lowa-nonce')) );
}
add_shortcode( 'Lowa-Add-Menu', 'lowa_addmenu' );
add_action('wp_enqueue_scripts', 'lowa_scripts');
add_action('wp_ajax_add_menu', 'lowa_insertmenu');
?>