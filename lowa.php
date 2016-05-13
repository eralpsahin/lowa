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
  $menuTable = $wpdb->prefix.'lowa_menu'; //adding menus
  $ingredientTable = $wpdb->prefix.'lowa_ingredient'; 
  $containTable = $wpdb->prefix.'lowa_contain'; //adding ingredients to menus
  $tableTable = $wpdb->prefix.'lowa_table'; 
  $groupTable = $wpdb->prefix.'lowa_group'; 
  $orderTable = $wpdb->prefix.'lowa_order';
  $revenueTable = $wpdb->prefix.'lowa_revenue';
  $charset_collate = $wpdb->get_charset_collate();
  
  $sql = "CREATE TABLE IF NOT EXISTS $menuTable
    (
      m_price REAL NOT NULL,
      m_id VARCHAR(50),
      m_stock int DEFAULT 100 NOT NULL,
      m_name VARCHAR(200) NOT NULL,
      PRIMARY KEY(m_id)
    )$charset_collate;";
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  $wpdb->query($sql);

  
  $sql = "CREATE TABLE IF NOT EXISTS $ingredientTable
      (
        i_name VARCHAR(50),
        PRIMARY KEY(i_name)
      )$charset_collate;";
  $wpdb->query($sql);

 
 $sql = "CREATE TABLE IF NOT EXISTS $containTable
      (
        m_id VARCHAR(50),
         i_name VARCHAR(50),
         PRIMARY KEY(m_id,i_name),
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
        g_bill REAL DEFAULT 0,
        t_id int, 
        PRIMARY KEY(g_id),
        FOREIGN KEY(t_id) REFERENCES $tableTable(t_id)
      )$charset_collate;";
  $wpdb->query($sql);

  $sql = "CREATE TABLE IF NOT EXISTS $orderTable
      (
        g_id int,
        m_id VARCHAR(50),
        o_num int,
        PRIMARY KEY(g_id,m_id),
        FOREIGN KEY(m_id) REFERENCES $menuTable(m_id),
        FOREIGN KEY(g_id) REFERENCES $groupTable(g_id)
      )$charset_collate;";
  $wpdb->query($sql);

  $sql = "CREATE TABLE IF NOT EXISTS $revenueTable
    (
      rest VARCHAR(1) DEFAULT '1' NOT NULL,
      revenue REAL DEFAULT 0 NOT NULL,
      PRIMARY KEY(rest)
    )$charset_collate;";
  $wpdb->query($sql);
  $wpdb->query("INSERT IGNORE INTO $revenueTable (rest,revenue) VALUES (1,0)");
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
  $revenueTable = $wpdb->prefix.'lowa_revenue';
  $sql = "DROP TABLE  $menuTable";
  $wpdb->query($sql);
  $sql = "DROP TABLE  $ingredientTable";
  $wpdb->query($sql);
  $sql = "DROP TABLE $containTable";
  $wpdb->query($sql);
  $sql = "DROP TABLE  $tableTable";
  $wpdb->query($sql);
  $sql = "DROP TABLE  $groupTable";
  $wpdb->query($sql);
  $sql = "DROP TABLE  $orderTable";
  $wpdb->query($sql);
  $sql = "DROP TABLE $revenueTable";
  $wpdb->query($sql);

}


//TODO shortcode for inserting new meal to the menu
function lowa_addmenu()
{
  ob_start();
  if(is_user_logged_in())
  {
    
    echo '<form id="add-menu-form" method="post" action="lowa.php">';
      echo 'Input format examples are given in input placeholder<br>';
      echo 'Menu Id:*<br>';
     echo '<input type="text" id="menu_id" placeholder="C1 or NB3 or Y2" required><br>';
  echo' Menu Name:*<br>';
  echo '<input type="text" id="menu_name" placeholder="Lamb Meat Soup or Spicy Cucumber Salad" required><br><br>';
  echo' Menu Price:*<br>';
  echo '<input type="text" id="menu_price" placeholder="15.25 or 16" required><br><br>';
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
      $menu_id =$_POST['menu_id'];
      $menu_price =$_POST['menu_price'];
      $menu_name = $_POST['menu_name'];
      $menu_stock = $_POST['menu_stock'];

    global $wpdb; //required global declaration of WP variable
      $menuTable = $wpdb->prefix.'lowa_menu';

      if($wpdb->insert( 
            $menuTable, 
            array( 
              'm_price' => $menu_price, 
              'm_id' => $menu_id,
              'm_stock' => $menu_stock,
              'm_name' => $menu_name
            )))
      echo 'finished';
    else
      echo 'failed insertion';
   }
  else
  {
    echo 'not entered';
  }
    die();
}

function lowa_addIngredient()
{
  ob_start();
  if(is_user_logged_in())
  {
    global $wpdb;
    $menuTable = $wpdb->prefix.'lowa_menu';
    $ingredientTable = $wpdb->prefix.'lowa_ingredient';
    $menus = $wpdb->get_results("
        SELECT m_id,m_name
        FROM $menuTable
        ");
    $ingredients = $wpdb->get_results("

        SELECT i_name
        FROM $ingredientTable
        ");
    echo count($menus).'<br>';
    echo count($ingredients);
    echo '<form id="add-ingredient-form" method="post" action="lowa.php">';
    echo' Choose Menu to add Ingredient:<br>';
    echo '<select id="menulist" form="add-ingredient-form" required>';
    foreach ($menus as $menu)
      {
        echo '<option value="'.$menu->m_id.'">'.$menu->m_name.'</option>';
      }
      echo '</select>';
      echo '</br></br>';
      echo' Choose Ingredient to add:<br>';
    echo '<select id="ingredientlist" form="add-ingredient-form" required>';
    foreach ($ingredients as $ingredient)
      {
        echo '<option value="'.$ingredient->i_name.'">'.$ingredient->i_name.'</option>';
      }
      echo '</select>';
      echo '</br></br>';
      echo '<input type="submit" value="Add Ingredient to Menu">';
    echo '</form>';
  }
  return ob_get_clean();
}

function lowa_insertIngredient()
{
  if ( isset( $_POST['menu_id'] ) && isset( $_POST['i_name'] ) && wp_verify_nonce($_POST['lowa_nonce'], 'lowa-nonce') )
    {
      $m_id = $_POST['menu_id'];
      $i_name = $_POST['i_name'];
      global $wpdb; //required global declaration of WP variable
      $containTable = $wpdb->prefix.'lowa_contain';
      $wpdb->insert( 
            $containTable, 
            array( 
              'm_id' => $m_id,
              'i_name' => $i_name
            ));
      echo 'finished';
    }
    die();
}

function lowa_addGroup()
{
  ob_start();
  if(is_user_logged_in())
  {
    echo'<form id="add-group-form" method="post" action="action_page.php">
  Enter number of people for the group:<br>
  <input type="number" id="num_people" min="1" max ="10" value="2" required><br><br>
  <input type="hidden" id="t_id">
  <input type="hidden" id="n_people">
  <input type="submit" id="btn" value="Find corresponding table">
</form>';
    
  }
  return ob_get_clean();
}
function lowa_findIngredient()
{
   ob_start();
  if(is_user_logged_in())
  {
    global $wpdb; //required global declaration of WP variable
    $ingredientTable = $wpdb->prefix.'lowa_ingredient';     
      $ingredients = $wpdb->get_results("

        SELECT i_name
        FROM $ingredientTable
        ");
    echo '<form id="find-menu-form" method="post" action="action_page.php">';
    echo' Choose Ingredient to search for:<br>';
    echo '<select id="ingredientlist" form="add-ingredient-form" required>';
    foreach ($ingredients as $ingredient)
      {
        echo '<option value="'.$ingredient->i_name.'">'.$ingredient->i_name.'</option>';
      }
      echo '</select>';
      echo '</br></br>';
      echo '<input type="submit" value="Find Menus with the Ingredient">';
    echo '</form>';
  }

  return ob_get_clean();
}

function lowa_findTable()
{
  if ( isset( $_POST['num_people'] )  && wp_verify_nonce($_POST['lowa_nonce'], 'lowa-nonce'))
  {
    global $wpdb; //required global declaration of WP variable
    $num_people = $_POST['num_people'];
    $tableTable = $wpdb->prefix.'lowa_table'; 
    $groupTable = $wpdb->prefix.'lowa_group'; 
    $t_id = $wpdb->get_var("
      SELECT T.t_id
      FROM $tableTable T
      WHERE T.t_capacity>=$num_people AND T.t_id NOT IN 
      (SELECT G.t_id FROM $groupTable G)
      ORDER BY T.t_capacity ASC
      LIMIT 1");
    echo $t_id;
  }
  die();
}
function lowa_findMenu(){

if ( isset( $_POST['ingredient'] ) && wp_verify_nonce($_POST['lowa_nonce'], 'lowa-nonce'))
  {
    global $wpdb;
   $containTable = $wpdb->prefix.'lowa_contain';
   $ingredient = $_POST['ingredient'];
   $menuTable = $wpdb->prefix.'lowa_menu';
   $menus = $wpdb->get_results("
        SELECT *
        FROM $menuTable M
        INNER JOIN $containTable C
        ON M.m_id=C.m_id
        WHERE C.i_name='$ingredient'
        ");
      foreach ($menus as $menu)
      {
        echo $menu->m_name;
        echo ' -- ';
      }
  }
  die();

}

function lowa_insertGroup()
{
  if ( isset( $_POST['num_people'] )  && isset( $_POST['t_id'] ) && wp_verify_nonce($_POST['lowa_nonce'], 'lowa-nonce'))
  {
    global $wpdb;
    $groupTable = $wpdb->prefix.'lowa_group';
    $num_people = $_POST['num_people'];
    $t_id = $_POST['t_id'];
     if($wpdb->insert( 
             $groupTable, 
            array( 
              'g_people' => $num_people, 
              't_id' => $t_id
            )))
    echo 'finished';
  }
  die();

}
function lowa_addOrder()
{
  ob_start();
  if(is_user_logged_in())
  {
    global $wpdb;
    $groupTable = $wpdb->prefix.'lowa_group';
    $groups =$wpdb->get_results("
        SELECT *
        FROM $groupTable
        ");
    $menuTable = $wpdb->prefix.'lowa_menu';
    $menus = $wpdb->get_results("
        SELECT *
        FROM $menuTable
        ");
    echo '<form id="add-order-form" method="post" action="action_page.php">';
    echo 'Add order to group number :<br/>';
    foreach ($groups as $group)
    {
      echo '<input type="radio" name="groupID" value="'.$group->g_id.'" required> Group with id: '.$group->g_id.', current bill is: '.$group->g_bill.'<br/>';
    }
    echo '<br/><br/>';
    echo 'Order Menu :<br/>';
    foreach ($menus as $menu)
    {
      if($menu->m_stock>0)
        echo '<input type="radio" data-val="'.$menu->m_stock.'" class="menuID" name="menuid" value="'.$menu->m_id.'" required>'.$menu->m_name.', Remaining stock: '.$menu->m_stock.'<br/>';
      else
        echo '<input type="radio" class="menuID" value="'.$menu->m_id.'" disabled>'.$menu->m_name.', Remaining stock: '.$menu->m_stock.'<br/>';
    }
    echo '<div id="qu">';
    echo 'Quantity :<br/>';
    echo '<input type="number" id="quantity" min="1" max="10" step="1" value="1" required>';
    echo '</div>';
    echo '</br></br>';
    echo '<input type="submit" value="Place an order">';
    echo '</from>';

  }
  return ob_get_clean();
}
function lowa_placeOrder(){
  if ( isset( $_POST['menu_id'] )  && isset( $_POST['quantity'] )  && isset( $_POST['group_id'] ) && wp_verify_nonce($_POST['lowa_nonce'], 'lowa-nonce'))
  {
    global $wpdb;
    $menuTable = $wpdb->prefix.'lowa_menu'; //adding menus
    $groupTable = $wpdb->prefix.'lowa_group'; 
    $orderTable = $wpdb->prefix.'lowa_order';
    $group = $_POST['group_id'];
    $menu = $_POST['menu_id'];
    $quantity = $_POST['quantity'];
    $wpdb->query("
              INSERT INTO $orderTable (g_id,m_id,o_num) 
              VALUES ($group,'$menu',$quantity) 
              ON DUPLICATE KEY UPDATE o_num=o_num+$quantity
              ");
    $price = $wpdb->get_var("SELECT m_price FROM $menuTable WHERE m_id='$menu'");
    $wpdb->query("
              UPDATE $groupTable SET g_bill=g_bill+($price*$quantity) WHERE g_id = $group
              ");
    $wpdb->query("
              UPDATE $menuTable SET m_stock=m_stock-$quantity WHERE m_id = '$menu'
              ");
    echo 'Group '.$group.' ordered '.$quantity.' menu '.$menu.'(s)';
  }
  die();
}
function lowa_statistics(){
  ob_start();
  if(is_user_logged_in())
  {
    global $wpdb;
    $menuTable = $wpdb->prefix.'lowa_menu'; //adding menus
    $ingredientTable = $wpdb->prefix.'lowa_ingredient'; 
    $containTable = $wpdb->prefix.'lowa_contain'; //adding ingredients to menus
    $tableTable = $wpdb->prefix.'lowa_table'; 
    $groupTable = $wpdb->prefix.'lowa_group'; 
    $orderTable = $wpdb->prefix.'lowa_order';
    $revenueTable = $wpdb->prefix.'lowa_revenue';
    echo 'Remaining empty seats currently for the tables:<br/>';
    $tables=$wpdb->get_results("
              SELECT T.t_id, (T.t_capacity - G.g_people) AS remaining
              FROM $tableTable T, $groupTable G
              WHERE G.t_id = T.t_id
              UNION
              SELECT T.t_id, T.t_capacity AS remaining
              FROM $tableTable T
              WHERE T.t_id NOT IN(SELECT t_id FROM $groupTable)
              ORDER BY remaining DESC;
      ");
    echo '<table style="width:100%">';
    echo '<tr>
    <th>Table ID</th>
    <th>Remaining free seats</th> 
  </tr>';
    foreach($tables as $table)
    {
      if($table->remaining!=0){
        echo '<tr>';
        echo '<td>'.$table->t_id.'</td>';
        echo '<td>'.$table->remaining.'</td>';
        echo '</tr>';
      }
    }
    echo '</table><br/>';

    $totalfrees = $wpdb->get_results("
      SELECT T.t_id, T.t_capacity - 
      ( 
      SELECT SUM( g_people ) 
      FROM $groupTable G
      WHERE G.t_id = T.t_id ) AS remaining
      FROM $tableTable T
      ");
   
    $totalfreeseats=0;
    foreach($totalfrees as $totalfree)
    {
      if($totalfree->remaining!=NULL)
      $totalfreeseats+=$totalfree->remaining;
      else{
        $count=$wpdb->get_var("SELECT t_capacity FROM $tableTable WHERE t_id=$totalfree->t_id");
        $totalfreeseats+=$count;
      }
    }
    
      echo'Currently total of '.$totalfreeseats.' remaining seat(s).<br/><br/>';
    

    $totalseated = $wpdb->get_var("
      SELECT SUM(g_people) AS total
      FROM $groupTable
      ");
     echo'Currently total of '.$totalseated.' customer(s) is in the restaurant.<br/><br/>';

     $totalmenu = $wpdb->get_var("SELECT COUNT(*) FROM $menuTable");

     echo 'There are '.$totalmenu.' menu(s) in the menu list<br/><br/>';

     $revenue=$wpdb->get_var("SELECT revenue FROM $revenueTable WHERE rest=1");

     echo 'Total revenue of the Restaurant is '.$revenue.'<br/><br/>';
  }

  return ob_get_clean();
}

function lowa_menuList(){

  ob_start();
  if(is_user_logged_in())
  {
    global $wpdb;
    $menuTable = $wpdb->prefix.'lowa_menu'; //adding menus
    $orderby = isset($_POST['orderby']) ? $_POST['orderby'] : 'm_name' ;
    $menus = $wpdb->get_results("SELECT * FROM $menuTable ORDER BY $orderby");

    echo '<table style="width:120%">';
    echo '<tr>
    <th>Menu ID</th>
    <th>Menu Name</th>
    <th>Menu Price</th>
    <th>Remaining Stock</th> 
    </tr>';

    foreach($menus as $menu)
    {
        echo '<tr>';
        echo '<td>'.$menu->m_id.'</td>';
        echo '<td>'.$menu->m_name.'</td>';
        echo '<td>'.$menu->m_price.'</td>';
        echo '<td>'.$menu->m_stock.'</td>';
        echo '</tr>';
    }
    echo '</table>';
   echo' <form id="form" method="post">';
    echo'<select name="orderby" form="form">';
    echo'<option value="m_name">Name: Alphabetically</option>';
    echo'<option value="m_name DESC">Name: Reverse alphabetically</option>';

    echo'<option value="m_id">Id: Alphabetically</option>';
    echo'<option value="m_id DESC">Id: Reverse alphabetically</option>';

    echo'<option value="m_price">Price: Lowest First</option>';
    echo'<option value="m_price DESC">Price: Highest First</option>';

    echo'<option value="m_stock">Stock: Lowest First</option>';
    echo'<option value="m_stock DESC">Stock: Highest First</option>';
    echo'</select><br/>';
    echo'<input type="submit" value="Sort"></form>';
  }

  return ob_get_clean();
}
function lowa_finishAccount()
{
  ob_start();
  if(is_user_logged_in())
  {
    global $wpdb;
    $menuTable = $wpdb->prefix.'lowa_menu'; //adding menus
    $orderby = isset($_POST['orderby']) ? $_POST['orderby'] : 'm_name' ;
    $groupTable = $wpdb->prefix.'lowa_group'; 
    $groups =$wpdb->get_results("
        SELECT *
        FROM $groupTable
        ");
    echo '<form id="close-account-form" method="post" action="action_page.php">';
    echo 'Add order to group number :<br/>';
    foreach ($groups as $group)
    {
      if($group->g_bill==0)
          echo '<input type="radio" name="groupID" disabled value="'.$group->g_id.'" required> Group with id: '.$group->g_id.', current bill is: '.$group->g_bill.'<br/>';
        
      else
          echo '<input type="radio" name="groupID" value="'.$group->g_id.'" required> Group with id: '.$group->g_id.', current bill is: '.$group->g_bill.'<br/>';
    }
    echo'<input type="submit" value="Close Account"></form>';
    
  }

  return ob_get_clean();
}
function lowa_finish_accont()
{
  if (isset( $_POST['group_id'] ) && wp_verify_nonce($_POST['lowa_nonce'], 'lowa-nonce'))
  {
    global $wpdb;
    $revenueTable = $wpdb->prefix.'lowa_revenue';
    $groupTable = $wpdb->prefix.'lowa_group'; 
    $group_id = $_POST['group_id'];
    $g_bill = $wpdb->get_var("SELECT g_bill FROM $groupTable WHERE g_id = $group_id");
    $wpdb->query("UPDATE $revenueTable SET revenue=revenue+$g_bill");
    $wpdb->query("DELETE FROM $groupTable WHERE g_id = $group_id");
    echo 'Group '.$group_id.' left the restaurant '.$g_bill.' added to the Restaurants revenue';
  }
  die();
}

function lowa_scripts() {
    wp_enqueue_script('lowa-script', plugins_url() . '/lowa-plugin/lowa.js', array( 'jquery' ), '1.0'); 

     wp_localize_script( 'lowa-script', 'lowadata', 
       array('ajaxurl' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('lowa-nonce')) );
}
add_shortcode( 'Lowa-Add-Menu', 'lowa_addmenu' );

add_shortcode( 'Lowa-Ingredient','lowa_findIngredient');

add_shortcode( 'Lowa-Add-Group', 'lowa_addGroup' );

add_shortcode( 'Lowa-Add-Ingredient', 'lowa_addIngredient' );

add_shortcode('Lowa-Statistics','lowa_statistics');

add_shortcode('Lowa-Menu','lowa_menuList');

add_shortcode('Lowa-Have-Check','lowa_finishAccount');

add_shortcode( 'Lowa-Add-Order', 'lowa_addOrder');

add_action('wp_enqueue_scripts', 'lowa_scripts');

add_action('wp_ajax_add_menu', 'lowa_insertmenu');

add_action('wp_ajax_place_order', 'lowa_placeOrder');

add_action('wp_ajax_find_table', 'lowa_findTable');

add_action('wp_ajax_find_menu', 'lowa_findMenu');

add_action('wp_ajax_finish_account','lowa_finish_accont');

add_action('wp_ajax_add_ingredient', 'lowa_insertIngredient');

add_action('wp_ajax_insert_group', 'lowa_insertGroup');

?>