<?php
/**
 * admin/profile.php
 * ============================================================
 * Administrator Profile Settings Dashboard
 * The Global Rise Foundation
 *
 * Allows administrators to update their personal details,
 * manage security settings (password change), and upload custom avatars.
 * ============================================================
 */

include './header.php'; // Starts session, executes auth-check middleware and DB connection

// Generate CSRF token if not set
if (empty($_SESSION['profile_csrf_token'])) {
    $_SESSION['profile_csrf_token'] = bin2hex(random_bytes(32));
}

$success_msg = '';
$error_msg = '';

// Process Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action     = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Verify CSRF token
    if (!hash_equals($_SESSION['profile_csrf_token'], $csrf_token)) {
        $error_msg = 'Security check failed. Invalid token validation (CSRF).';
    } else {
        $pdo = getDB();

        // Action 1: Update Details
        if ($action === 'update_details') {
            $name     = trim($_POST['name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $mobile   = trim($_POST['mobile'] ?? '');

            if (empty($name) || empty($email) || empty($username)) {
                $error_msg = 'Full name, email address, and username are required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_msg = 'Please enter a valid email address.';
            } elseif (!empty($mobile) && !preg_match('/^[6-9]\d{9}$/', $mobile)) {
                $error_msg = 'Please enter a valid 10-digit Indian mobile number.';
            } else {
                try {
                    // Check email uniqueness
                    $chkEmail = $pdo->prepare("SELECT COUNT(*) FROM `admins` WHERE `email` = :email AND `id` != :id");
                    $chkEmail->execute([':email' => $email, ':id' => $current_admin['id']]);
                    if ((int)$chkEmail->fetchColumn() > 0) {
                        $error_msg = 'This email address is already registered to another administrator.';
                    } else {
                        // Check username uniqueness
                        $chkUser = $pdo->prepare("SELECT COUNT(*) FROM `admins` WHERE `username` = :username AND `id` != :id");
                        $chkUser->execute([':username' => $username, ':id' => $current_admin['id']]);
                        if ((int)$chkUser->fetchColumn() > 0) {
                            $error_msg = 'This username is already taken by another administrator.';
                        } else {
                            // Save profile changes
                            $update = $pdo->prepare("
                                UPDATE `admins`
                                SET `name` = :name, `email` = :email, `username` = :username, `mobile` = :mobile
                                WHERE `id` = :id
                            ");
                            $update->execute([
                                ':name'     => $name,
                                ':email'    => $email,
                                ':username' => $username,
                                ':mobile'   => $mobile,
                                ':id'       => $current_admin['id']
                            ]);

                            // Refresh page bindings
                            $current_admin['name']      = $name;
                            $current_admin['email']     = $email;
                            $current_admin['username']  = $username;
                            $current_admin['mobile']    = $mobile;
                            $_SESSION['admin_name']     = $name;
                            $_SESSION['admin_username'] = $username;

                            $success_msg = 'Your profile details have been updated successfully.';
                        }
                    }
                } catch (PDOException $e) {
                    error_log('[Profile Update Error] Exception: ' . $e->getMessage());
                    $error_msg = APP_DEBUG ? 'Database Error: ' . $e->getMessage() : 'An error occurred while updating details.';
                }
            }
        }

        // Action 2: Update Profile Image/Avatar
        elseif ($action === 'update_avatar') {
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                $error_msg = 'No file was selected or an upload error occurred.';
            } else {
                $file = $_FILES['avatar'];
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
                $allowed_exts  = ['jpg', 'jpeg', 'png', 'webp'];

                // Validate actual file type (MIME)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!in_array($mime, $allowed_mimes) || !in_array($ext, $allowed_exts)) {
                    $error_msg = 'Invalid file format. Only JPEG, PNG, and WEBP formats are accepted.';
                } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB
                    $error_msg = 'The image file size is too large. Maximum size allowed is 2MB.';
                } else {
                    $upload_dir = __DIR__ . '/../assets/uploads/admin/';
                    
                    // Create directory recursively with safe permissions
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Build unique, secure file name
                    $new_filename = 'admin_' . $current_admin['id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $dest_path    = $upload_dir . $new_filename;

                    if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                        try {
                            $old_img = $current_admin['profile_image'];
                            $db_path = 'assets/uploads/admin/' . $new_filename;

                            $update = $pdo->prepare("UPDATE `admins` SET `profile_image` = :img WHERE `id` = :id");
                            $update->execute([
                                ':img' => $db_path,
                                ':id'  => $current_admin['id']
                            ]);

                            // Unlink old avatar image if it exists and was custom uploaded
                            if (!empty($old_img) && file_exists(__DIR__ . '/../' . $old_img)) {
                                unlink(__DIR__ . '/../' . $old_img);
                            }

                            $current_admin['profile_image'] = $db_path;
                            $success_msg = 'Your profile picture has been updated successfully.';
                        } catch (PDOException $e) {
                            error_log('[Profile Avatar Error] DB Exception: ' . $e->getMessage());
                            $error_msg = 'Database error: ' . $e->getMessage();
                        }
                    } else {
                        $error_msg = 'An error occurred while saving the uploaded image file.';
                    }
                }
            }
        }

        // Action 3: Change Password
        elseif ($action === 'change_password') {
            $current_pwd = $_POST['current_password'] ?? '';
            $new_pwd     = $_POST['new_password']     ?? '';
            $confirm_pwd = $_POST['confirm_password'] ?? '';

            if (empty($current_pwd) || empty($new_pwd) || empty($confirm_pwd)) {
                $error_msg = 'Please fill out all the password credentials.';
            } elseif ($new_pwd !== $confirm_pwd) {
                $error_msg = 'The new passwords entered do not match.';
            } elseif (strlen($new_pwd) < 8) {
                $error_msg = 'The new password must be at least 8 characters long.';
            } else {
                try {
                    // Pull verified password hash
                    $chk = $pdo->prepare("SELECT `password` FROM `admins` WHERE `id` = :id LIMIT 1");
                    $chk->execute([':id' => $current_admin['id']]);
                    $db_hash = $chk->fetchColumn();

                    if (!$db_hash || !password_verify($current_pwd, $db_hash)) {
                        $error_msg = 'The current password you entered is incorrect.';
                    } else {
                        // Generate secure bcrypt hash
                        $new_hash = password_hash($new_pwd, PASSWORD_BCRYPT, ['cost' => 12]);
                        $update   = $pdo->prepare("UPDATE `admins` SET `password` = :pwd WHERE `id` = :id");
                        $update->execute([
                            ':pwd' => $new_hash,
                            ':id'  => $current_admin['id']
                        ]);

                        $success_msg = 'Your login password has been changed successfully.';
                    }
                } catch (PDOException $e) {
                    error_log('[Profile Security Error] Exception: ' . $e->getMessage());
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}
?>

<div class="row">
    <!-- Left Profile Side Card -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center position-relative mb-3">
                    <img class="profile-user-img img-fluid img-circle bg-white" 
                         src="<?= !empty($current_admin['profile_image']) ? htmlspecialchars('../' . $current_admin['profile_image']) : './src/images/user-avtar.png' ?>" 
                         alt="User profile picture"
                         style="width: 110px; height: 110px; object-fit: cover; border: 3px solid #dcdcde;">
                </div>
                <h3 class="profile-username text-center font-weight-bold" style="color: var(--sidebar-color);"><?= htmlspecialchars($current_admin['name']) ?></h3>
                <p class="text-muted text-center">@<?= htmlspecialchars($current_admin['username']) ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Email Address</b> <span class="float-right text-dark font-weight-500"><?= htmlspecialchars($current_admin['email']) ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Mobile Number</b> <span class="float-right text-dark font-weight-500"><?= htmlspecialchars($current_admin['mobile']) ?: 'Not Set' ?></span>
                    </li>
                </ul>

                <!-- Upload Avatar Form -->
                <form action="profile.php" method="POST" enctype="multipart/form-data" id="avatarForm">
                    <input type="hidden" name="action" value="update_avatar">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['profile_csrf_token']) ?>">
                    
                    <div class="form-group mb-0 text-center">
                        <div class="custom-file d-none">
                            <input type="file" class="custom-file-input" id="avatarInput" name="avatar" accept="image/png, image/jpeg, image/webp" onchange="submitAvatar()">
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-block btn-sm" onclick="document.getElementById('avatarInput').click();">
                            <i class="fas fa-camera mr-1"></i> Upload Profile Image
                        </button>
                        <small class="form-text text-muted mt-2">Supports JPG, PNG or WEBP (Max 2MB)</small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Settings Fields Form Tab panels -->
    <div class="col-md-8">
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1" style="background-color: var(--primary-blue) !important; border-bottom: none !important;">
                <ul class="nav nav-tabs" id="profileTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab" aria-controls="details" aria-selected="true">
                            <i class="fas fa-user-edit mr-1"></i> General Details
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="security-tab" data-toggle="pill" href="#security" role="tab" aria-controls="security" aria-selected="false">
                            <i class="fas fa-user-lock mr-1"></i> Security Settings
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="profileTabContent">
                    
                    <!-- General details settings panel -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <form action="profile.php" method="POST" class="form-horizontal">
                            <input type="hidden" name="action" value="update_details">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['profile_csrf_token']) ?>">

                            <div class="form-group row">
                                <label for="inputName" class="col-sm-3 col-form-label">Full Name</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="inputName" name="name" placeholder="Enter Full Name" value="<?= htmlspecialchars($current_admin['name']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-3 col-form-label">Email Address</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Enter Email Address" value="<?= htmlspecialchars($current_admin['email']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputUsername" class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Enter Username" value="<?= htmlspecialchars($current_admin['username']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputMobile" class="col-sm-3 col-form-label">Mobile Number</label>
                                <div class="col-sm-9">
                                    <input type="tel" class="form-control" id="inputMobile" name="mobile" placeholder="10-digit Indian Mobile" value="<?= htmlspecialchars($current_admin['mobile']) ?>">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="offset-sm-3 col-sm-9">
                                    <button type="submit" class="btn btn-success" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; padding: 7px 20px;">
                                        <i class="fas fa-save mr-1"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Security details settings panel -->
                    <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <form action="profile.php" method="POST" class="form-horizontal">
                            <input type="hidden" name="action" value="change_password">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['profile_csrf_token']) ?>">

                            <div class="form-group row">
                                <label for="inputCurrentPassword" class="col-sm-3 col-form-label">Current Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="inputCurrentPassword" name="current_password" placeholder="Enter current login password" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputNewPassword" class="col-sm-3 col-form-label">New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="inputNewPassword" name="new_password" placeholder="Minimum 8 characters" required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="inputConfirmPassword" class="col-sm-3 col-form-label">Confirm New Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="inputConfirmPassword" name="confirm_password" placeholder="Confirm new password" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="offset-sm-3 col-sm-9">
                                    <button type="submit" class="btn btn-primary" style="background-color: var(--primary-blue) !important; border-color: var(--primary-blue) !important; padding: 7px 20px;">
                                        <i class="fas fa-key mr-1"></i> Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitAvatar() {
    var fileInput = document.getElementById('avatarInput');
    if (fileInput.files.length > 0) {
        // Show loading alert before submitting
        Swal.fire({
            title: 'Uploading...',
            text: 'Saving your profile picture.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        document.getElementById('avatarForm').submit();
    }
}
</script>

<?php include './footer.php'; ?>

<!-- SweetAlert notification display handlers -->
<?php if (!empty($success_msg)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Updated!',
            text: '<?= htmlspecialchars($success_msg) ?>',
            icon: 'success',
            confirmButtonColor: '#13a34a'
        });
    });
</script>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Error!',
            text: '<?= htmlspecialchars($error_msg) ?>',
            icon: 'error',
            confirmButtonColor: '#1b5182'
        });
    });
</script>
<?php endif; ?>