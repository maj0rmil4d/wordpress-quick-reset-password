<?php
$passphrase = readline("Enter the passphrase: ");
if ($passphrase !== "your_secret_passphrase") {
    echo "Incorrect passphrase. Exiting.\n";
    exit;
}
include_once("wp-config.php");
global $wpdb;
$my_filename = basename($_SERVER['PHP_SELF']);

if (isset($_GET["resetid"]) && $_GET["resetid"] && isset($_GET["user_login"]) && $_GET["user_login"]) {
    $show_page = "new";
    $resetid = $_GET["resetid"];
    $user_login = $_GET["user_login"];
    $new_password = "AtakanAu_" . rand(10000000, 99999999);
    $sql = "UPDATE " . $table_prefix . "users SET user_pass=MD5('" . $new_password . "') WHERE ID=" . $resetid . " AND user_login='" . $user_login . "'";
    $results_reset = (int) $wpdb->query($sql);
    if ($results_reset) {
        $message = "Password updated.";
    } else {
        $show_page = "error";
        $error = "SQL update error.";
    }
} elseif (
    isset($_GET["new_user"]) &&
    isset($_POST["user_login"]) && $_POST["user_login"] &&
    isset($_POST["user_nicename"]) &&
    isset($_POST["user_email"]) &&
    isset($_POST["display_name"])
) {
    $show_page = "new";
    $i = 0;

    $sql = "SELECT MAX(ID) as max_id FROM " . $table_prefix . "users";
    $result_max_id = $wpdb->get_row($sql);
    $new_id = 1 + (int) $result_max_id->max_id;

    $user_login = $_POST["user_login"];
    $user_nicename = $_POST["user_nicename"];
    $user_email = $_POST["user_email"] ? $_POST["user_email"] : "user" . $new_id . "@domain.com";
    $display_name = $_POST["display_name"];
    $date_now = $mytime = date('Y-m-d H:i:s', time());
    $new_password = "AtakanAu_" . rand(10000000, 99999999);

    $sql = "INSERT INTO `" . DB_NAME . "`.`" . $table_prefix . "users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) "
        . "VALUES('" . $new_id . "','" . $user_login . "',MD5('" . $new_password . "'),'" . $display_name . "','" . $user_email . "','https://atakanau.blogspot.com','" . $date_now . "','','0','" . $display_name . "')";
    $results_new = $wpdb->query($sql);
    $i += (int) $results_new;

    $sql = "INSERT INTO `" . DB_NAME . "`.`" . $table_prefix . "usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) "
        . "VALUES(NULL, '" . $new_id . "','" . $table_prefix . "capabilities','a:1:{s:13:\"administrator\";s:1:\"1\";}')";
    $results_new = $wpdb->query($sql);
    $i += (int) $results_new;

    $sql = "INSERT INTO `" . DB_NAME . "`.`" . $table_prefix . "usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) "
        . "VALUES(NULL, '" . $new_id . "','" . $table_prefix . "user_level','10')";
    $results_new = $wpdb->query($sql);
    $i += (int) $results_new;

    if ($i == 3) {
        $show_page = "new";
        $message = "Created a new user.";
    } else {
        $show_page = "error";
        $error = "SQL insert error.";
    }
} else {
    $show_page = "list";
    $sql = "SELECT * FROM " . $table_prefix . "users ORDER BY ID";
    $result_users = $wpdb->get_results($sql);
}
?>
<!DOCTYPE html>
<html lang="tr-TR">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WordPress User Password Tool - Atakan Au</title>

    <link rel='stylesheet' type='text/css' media='all' href='wp-includes/css/buttons.min.css' />
    <link rel='stylesheet' type='text/css' media='all' href='wp-admin/css/login.min.css' />
    <link rel='stylesheet' type='text/css' media='all' href='wp-admin/css/common.css' />
    <link rel='stylesheet' type='text/css' media='all' href='wp-includes/css/dashicons.css' />

    <meta name="viewport" content="width=device-width" />
</head>

<body class="wp-core-ui">

    <div class="update-message notice inline notice-alt notice-error">
        <p>Delete this PHP file named "<?php echo $my_filename; ?>" after using it. <a href="https://atakanau.blogspot.com/2021/06/wordpress-user-password-reset.html">atakanau.blogspot.com</a></p>
    </div>
    <?php if ($show_page == "list") { ?>
        <table cellpadding="2" cellspacing="0" border="1" align="center">
            <thead>
                <tr>
                    <th> DB_NAME </th>
                    <th> DB_USER </th>
                    <th> DB_PASSWORD </th>
                    <th> table_prefix </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code class="filter-count"><b><?php echo DB_NAME ?></b></code><br></td>
                    <td><code class="filter-count"><b><?php echo DB_USER ?></b></code><br></td>
                    <td><code class="filter-count"><b><?php echo DB_PASSWORD ?></b></code><br></td>
                    <td><code class="filter-count"><b><?php echo $table_prefix ?></b></code><br></td>
                </tr>
            </tbody>
        </table>
        <br>
        <form name="" id="" action="<?php echo $my_filename ?>?new_user=1" method="post">
            <table cellpadding="0" align="center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>user_status</th>
                        <th>user_login</th>
                        <th>user_nicename</th>
                        <th>user_email</th>
                        <th>display_name</th>
                        <th>user_registered</th>
                        <th>Password</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result_users as $row) { ?>
                        <tr>
                            <td><?php echo $row->ID ?></td>
                            <td><?php echo $row->user_status ?></td>
                            <td><?php echo $row->user_login ?></td>
                            <td><?php echo $row->user_nicename ?></td>
                            <td><?php echo $row->user_email ?></td>
                            <td><?php echo $row->display_name ?></td>
                            <td><?php echo $row->user_registered ?></td>
                            <td><a class="button-link-delete button button-small" href="?resetid=<?php echo $row->ID ?>&user_login=<?php echo $row->user_login ?>"><i class="dashicons dashicons-welcome-edit-page"></i> Reset</a></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><input value="" autocapitalize="none" placeholder="login (required)" name="user_login" class="input"></td>
                        <td><input value="" autocapitalize="none" placeholder="nicename" name="user_nicename" class="input"></td>
                        <td><input value="" autocapitalize="none" placeholder="email" name="user_email" class="input"></td>
                        <td><input value="" autocapitalize="none" placeholder="display name" name="display_name" class="input"></td>
                        <td><button type="submit" class="button button-small" value=""><i class='dashicons dashicons-plus-alt'></i> Create</button></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php } elseif ($show_page == "new") { ?>
            <div id="login">
                <div class="updated-message updated">
                    <p><i class="dashicons dashicons-wordpress"></i> <?php echo $message ?></p>
                </div>
                <form name="loginform" id="loginform">
                    <p>
                        <div><label><i class="dashicons dashicons-admin-users"></i> Login:</label></div>
                        <code class="filter-count widefat"><b><?php echo $user_login ?></b></code>
                    </p>
                    <div class="user-pass-wrap">
                        <div><label><i class="dashicons dashicons-admin-network"></i> Password:</label></div>
                        <code class="filter-count widefat"><b><?php echo $new_password ?></b></code>
                    </div>
                    <br>
                    <p>
                        <a class="button button-small" href="<?php echo $my_filename ?>"><i class="dashicons dashicons-arrow-left-alt"></i> Back</a>
                        <a class="button button-small" href="wp-login.php" target="_blank">Login <i class="dashicons dashicons-external"></i></a>
                    </p>
                </form>
            </div>
        <?php } elseif ($show_page == "error") { ?>
            <div id="setting-error-settings_updated" class="notice notice-error settings-error is-dismissible">
                <p><strong><?php echo $error ?></strong></p>
            </div>
            <a class="button button-small" href="<?php echo $my_filename ?>"><i class="dashicons dashicons-arrow-left-alt"></i> Back</a>
        <?php } ?>
</body>

</html>
