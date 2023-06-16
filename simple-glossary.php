<?php
/*
Plugin Name: Simple Glossary
Description: A simple glossary plugin for WordPress.
Version: 1.4
Author: Evan Grissino
Requires at least: 6.2
Requires PHP: 7.4
*/

// Call our installation and uninstallation functions when the plugin is activated or deactivated
register_activation_hook(__FILE__, 'simple_glossary_install');
register_deactivation_hook(__FILE__, 'simple_glossary_uninstall');

// Set up the glossary table on plugin activation
function simple_glossary_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'simple_glossary';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      term_name varchar(55) NOT NULL,
      term_description text NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Remove the glossary table on plugin deactivation
function simple_glossary_uninstall() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'simple_glossary';

    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}


add_action('admin_menu', 'simple_glossary_menu');

function simple_glossary_menu() {
    add_menu_page('Simple Glossary', 'Simple Glossary', 'manage_options', 'simple-glossary', 'simple_glossary_page_new', '', 6);
}

function simple_glossary_page() {
    if(isset($_POST['new_term_name']) && isset($_POST['new_term_description'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'simple_glossary';

        $wpdb->insert(
            $table_name,
            array(
                'term_name' => $_POST['new_term_name'],
                'term_description' => $_POST['new_term_description']
            )
        );
        echo "<h2>Term added successfully!</h2>";
    }
    echo '<form action="" method="post">
            <label for="new_term_name">Term:</label><br>
            <input type="text" id="new_term_name" name="new_term_name"><br>
            <label for="new_term_description">Description:</label><br>
            <textarea id="new_term_description" name="new_term_description" style="width:50%;"></textarea><br>
            <input type="submit" value="Submit">
          </form>';
}

function simple_glossary_page_new() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'simple_glossary';

    if(isset($_POST['new_term_name']) && isset($_POST['new_term_description'])) {
        $wpdb->insert(
            $table_name,
            array(
                'term_name' => $_POST['new_term_name'],
                'term_description' => $_POST['new_term_description']
            )
        );
        echo "<h2>Term added successfully!</h2>";
    }

    echo '<form action="" method="post">
            <label for="new_term_name">Term:</label><br>
            <input type="text" id="new_term_name" name="new_term_name"><br>
            <label for="new_term_description">Description:</label><br>
            <textarea id="new_term_description" name="new_term_description"></textarea><br>
            <input type="submit" value="Submit">
          </form>';

    // Fetch and display all glossary items
    //$results = $wpdb->get_results("SELECT * FROM $table_name");
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY term_name ASC");

    ?>
    <div class="wrap widefat">
      <h2>Glossary:</h2>
      <table class="wp-list-table widefat striped">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Definition</th>
          </tr>
        </thead>
        <tbody>
      <?php if($results){ ?>
      <?php foreach ($results as $row): ?>
          <tr>
              <td><strong><?php echo $row->term_name; ?></strong></td>
              <td><?php echo $row->term_description; ?></td>
          </tr>
      <?php endforeach; ?>
      <?php } ?>
        </tbody>
      </table>
    </div>
    <?php
}


// Shortcode to display glossary
add_shortcode('simple_glossary', 'display_glossary');

function display_glossary() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'simple_glossary';

    //$results = $wpdb->get_results("SELECT * FROM $table_name");
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY term_name ASC");
    $output = '<div class="wp-block-group is-vertical is-layout-flex wp-container-28">';
    foreach($results as $row){
      $output .= '<div class="wp-block-group glossary-row is-nowrap is-layout-flex wp-container-15">';
      $output .= '<div class="wp-block-group is-vertical is-layout-flex wp-container-13">';
      $output .= '<h4 class="wp-block-heading glossary-title" id="' . $row->term_name . '">' . $row->term_name . '</h4>';
      $output .= '<div style="height:150px" aria-hidden="true" class="wp-block-spacer"></div></div>';
      $output .= '<div class="wp-block-group is-vertical is-layout-flex wp-container-14">';
      $output .= '<p class="glossary-defition">'. $row->term_description . '</p>';
      $output .= '<div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div></div>';
      $output .= '</div>';
    }
    $output .= '</div>';

    return $output;
}

?>
