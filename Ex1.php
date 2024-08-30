<?php
session_start();

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [];
}
function validateUser($username, $email, $address, $phone, $gender) {
    if (empty($username) || empty($email) || empty($address) || empty($phone) || empty($gender)) {
        return "All fields must be fill.";
    }
    if (!is_numeric($phone) || strlen($phone) < 10 || strlen($phone) > 11) {
        return "Phone number must be a number with 10-11 digits.";
    }
    foreach ($_SESSION['users'] as $user) {
        if ($user['email'] === $email) {
            return "Email exits.";
        }
    }
    return true;
}
function addUser($username, $email, $address, $phone, $gender) {
    date_default_timezone_set('Asia/Ho_Chi_Minh'); 
    $userId = date('HisdmY');
    $_SESSION['users'][] = [
        'user_id' => $userId,
        'username' => $username,
        'email' => $email,
        'address' => $address,
        'phone' => $phone,
        'gender' => $gender
    ];
}

function editUser($userId, $username, $email, $address, $phone, $gender) {
    foreach ($_SESSION['users'] as &$user) {
        if ($user['user_id'] === $userId) {
            $user['username'] = $username;
            $user['email'] = $email;
            $user['address'] = $address;
            $user['phone'] = $phone;
            $user['gender'] = $gender;
            break;
        }
    }
}

function deleteUser($userId) {
    $_SESSION['users'] = array_filter($_SESSION['users'], function($user) use ($userId) {
        return $user['user_id'] !== $userId;
    });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $userId = $_POST['user_id'] ?? null;
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $gender = $_POST['gender'] ?? '';

    if ($action === 'add') {
        $validation = validateUser($username, $email, $address, $phone, $gender);
        if ($validation === true) {
            addUser($username, $email, $address, $phone, $gender);
        } else {
            echo $validation;
        }
    } elseif ($action === 'edit' && $userId) {
        editUser($userId, $username, $email, $address, $phone, $gender);
    } elseif ($action === 'delete' && $userId) {
        deleteUser($userId);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-center">User Management</h1>

    <div class="card mb-4">
        <div class="card-header">
            Add/Edit User
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $userId ?? ''; ?>">
                <input type="hidden" name="action" value="<?php echo isset($userId) ? 'edit' : 'add'; ?>">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" class="form-control" name="username" value="<?php echo $username ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $email ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Address:</label>
                    <input type="text" class="form-control" name="address" value="<?php echo $address ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone:</label>
                    <input type="text" class="form-control" name="phone" value="<?php echo $phone ?? ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Gender:</label>
                    <select class="form-select" name="gender">
                        <option value="male" <?php echo (isset($gender) && $gender == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo (isset($gender) && $gender == 'female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <h2 class="mb-4">User List</h2>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['users'] as $user): ?>
                <tr>
                    <td><?php echo $user['user_id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['address']; ?></td>
                    <td><?php echo $user['phone']; ?></td>
                    <td><?php echo ucfirst($user['gender']); ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <input type="hidden" name="action" value="edit">
                            <button type="submit" class="btn btn-warning btn-sm">Edit</button>
                        </form>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
