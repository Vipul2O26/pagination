<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include './db.php';

$id = $_GET['id'] ?? 0;

if ($id <= 0) die("Invalid ID");

$stmt = $connect->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) die("User not found");

$errors = [];

if (isset($_POST['submit'])) {

    $fname  = trim($_POST['fname'] ?? '');
    $lname  = trim($_POST['lname'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $status = $_POST['status'] ?? '';
    $gender = $_POST['gender'] ?? '';

   
    if ($fname == "" || strlen($fname) < 3)
        $errors['fname'] = "Minimum 3 characters required";

    if ($lname == "" || strlen($lname) < 3)
        $errors['lname'] = "Minimum 3 characters required";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = "Invalid email format";
    else {
        $check = $connect->prepare("SELECT id FROM users WHERE email=? AND id!=?");
        $check->execute([$email, $id]);
        if ($check->rowCount() > 0)
            $errors['email'] = "Email already exists";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone))
        $errors['phone'] = "Phone must be 10 digits";

    if (!in_array($status, ['married','unmarried']))
        $errors['status'] = "Invalid status";

    if (!in_array($gender, ['male','female','other']))
        $errors['gender'] = "Invalid gender";

    if (empty($errors)) {

        $update = $connect->prepare(
            "UPDATE users SET name=?, lastname=?, email=?, phone=?, status=?, gender=? WHERE id=?"
        );

        $update->execute([$fname,$lname,$email,$phone,$status,$gender,$id]);

        $_SESSION['msg'] = "User updated successfully";
        header("Location: list.php");
        exit;
    }
}

$fname  = $fname  ?? $user['name'];
$lname  = $lname  ?? $user['lastname'];
$email  = $email  ?? $user['email'];
$phone  = $phone  ?? $user['phone'];
$status = $status ?? $user['status'];
$gender = $gender ?? $user['gender'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>

</head>
<body>

    <div class="container mt-5">
        <div class="card p-4 shadow">
            <h4 class="text-center mb-4">Edit User</h4>
                <form action="edit.php?id=<?php echo $id; ?>" method="post" id="formDataId" novalidate>

                <!-- First Name -->
                <label>First Name *</label>
                <input type="text" name="fname"
                class="form-control <?= isset($errors['fname']) ? 'is-invalid' : (isset($_POST['submit']) && empty($errors['fname']) ? 'is-valid' : '') ?>"
                value="<?= htmlspecialchars($fname) ?>">
                <div class="invalid-feedback">
                    <?= $errors['fname'] ?? '' ?>
                </div>
                <br>

                <!-- Last Name -->
                <label>Last Name *</label>
                <input type="text" name="lname"
                class="form-control <?= isset($errors['lname']) ? 'is-invalid' : (isset($_POST['submit']) && empty($errors['lname']) ? 'is-valid' : '') ?>"
                value="<?= htmlspecialchars($lname) ?>">
                <div class="invalid-feedback">
                    <?= $errors['lname'] ?? '' ?>
                </div>
                <br>

                <!-- Email -->
                <label>Email *</label>
                <input type="email" name="email" id="email"
                class="form-control <?= isset($errors['email']) ? 'is-invalid' : (isset($_POST['submit']) && empty($errors['email']) ? 'is-valid' : '') ?>"
                value="<?= htmlspecialchars($email) ?>">
                <div class="invalid-feedback">
                    <?= $errors['email'] ?? '' ?>
                </div>
                <br>

                <!-- Phone -->
                <label>Phone *</label>
                <input type="text" name="phone"
                class="form-control <?= isset($errors['phone']) ? 'is-invalid' : (isset($_POST['submit']) && empty($errors['phone']) ? 'is-valid' : '') ?>"
                value="<?= htmlspecialchars($phone) ?>">
                <div class="invalid-feedback">
                    <?= $errors['phone'] ?? '' ?>
                </div>
                <br>

                <!-- Status -->
                <label>Status *</label>
                <select name="status"
                class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">
                    <option value="married" <?= $status=="married"?'selected':'' ?>>Married</option>
                    <option value="unmarried" <?= $status=="unmarried"?'selected':'' ?>>Unmarried</option>
                </select>
                <div class="invalid-feedback">
                    <?= $errors['status'] ?? '' ?>
                </div>
                <br>

                <!-- Gender -->
                <label>Gender *</label><br>
                <div class="<?= isset($errors['gender']) ? 'text-danger' : '' ?>">
                    <input type="radio" name="gender" value="male" <?= $gender=="male"?'checked':'' ?>> Male
                    <input type="radio" name="gender" value="female" <?= $gender=="female"?'checked':'' ?>> Female
                    <input type="radio" name="gender" value="other" <?= $gender=="other"?'checked':'' ?>> Other
                </div>
            
                <?php if(isset($errors['gender'])): ?>
                <div class="text-danger"><?= $errors['gender'] ?></div>
                <?php endif; ?>
                <br>

            <button type="submit" name="submit" class="btn btn-dark">Update</button>

        </form>
    </div>
    </div>

<script>
$(document).ready(function(){

    $("#formDataId").validate({

        rules:{
            fname:{ required:true, minlength:3 },
            lname:{ required:true, minlength:3 },
            email:{ required:true, email:true },
            phone:{ required:true, digits:true, minlength:10, maxlength:10 }
        },

        messages:{
            fname:"Minimum 3 characters required",
            lname:"Minimum 3 characters required",
            email:"Enter valid email",
            phone:"Enter 10 digit phone number"
        },

        highlight:function(element){
            $(element).addClass("is-invalid").removeClass("is-valid");
        },

        unhighlight:function(element){
            $(element).removeClass("is-invalid").addClass("is-valid");
        }

    });

});
</script>

</body>
</html>