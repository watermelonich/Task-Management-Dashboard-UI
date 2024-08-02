<?php 

require_once 'db_connect.php';
require_once 'auth_function.php';

checkAdminLogin();
$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $errors = [];

    $admin_id = $_SESSION['admin_id'];
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate fields
    if (empty($current_password)) {
        $errors[] = 'Current password is required.';
    }
    if (empty($new_password)) {
        $errors[] = 'New password is required.';
    }
    if (empty($confirm_password)) {
        $errors[] = 'Confirm password is required.';
    }
    if ($new_password !== $confirm_password) {
        $errors[] = 'New password and confirm password do not match.';
    }

    if (empty($errors)) {
        // Check if the current password is correct
        $stmt = $pdo->prepare("SELECT admin_password FROM task_admin WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $admin['admin_password'])) {
            // Update the password
            $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE task_admin SET admin_password = ? WHERE admin_id = ?");
            $stmt->execute([$new_password_hashed, $admin_id]);
            $success = true;
        } else {
            $errors[] = 'Current password is incorrect.';
        }
    }
}

include('header.php');

?>

<h1 class="mt-4">Change Password</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
    <li class="breadcrumb-item active">Change Password</a></li>
</ol>



<div class="row">
    <div class="col-md-4">
        <?php
        if(isset($errors) && is_countable($errors) && count($errors) > 0){
            echo '
            <div class="alert alert-danger">
                <ul class="list-unstyled">
            ';
            foreach($errors as $error){
                echo '<li>'.$error.'</li>';
            }
            echo '
                </ul>
            </div>
            ';
        }

        if($success){
            echo '
            <div class="alert alert-success">Password changed successfully</div>
            ';
        }
        ?>
        <div class="card">
            <div class="card-header"><b>Change Password</b></div>
            <div class="card-body">
                <form id="changePasswordForm" method="POST" action="admin_change_password.php">
                    <div class="mb-3">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>