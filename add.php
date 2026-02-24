<?php

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

include './db.php';

session_start();

// echo '<pre>';
// print_r( $_POST );
// echo '</pre>';

$fnameError = $lnameError = $emailError = $phoneError = $stausError = $genderError  = null;
$fnameFlag = $lnameFlag = $emailFlag = $phoneFlag = $stausFlag = $genderflag = true;

$fname = $lname = $email = $phone = $status = $gender = null;

function cleanData( $data ) {
    return htmlspecialchars(trim( $data ));
}

function checkAlphabhet( $string ) {
    return ctype_alpha( $string );
}

function checkNumber( $string ) {
    return ctype_digit( $string );
}

function getCurrentTime() {
    date_default_timezone_set( 'Asia/Kolkata' );

    $currentTimeIST = date( 'd-m-Y h:i:s A', time() );

    return $currentTimeIST;
}

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {

    //firstname

    if ( empty( $_POST[ 'fname' ] ) ) {
        $fnameError = 'name is required';
        $fnameFlag = false;
    } else if ( !checkAlphabhet( cleanData( $_POST[ 'fname' ] ) ) ) {
        $fnameError = 'only alphabhet allowed';
        $fnameFlag = false;
    } else if ( strlen( $_POST[ 'fname' ] ) < 3 ) {
        $fnameError = 'please enter at least 3 character';
        $fnameFlag = false;
    } else {
        $fname = $_POST[ 'fname' ];
        //echo $fname;
    }

    // echo $fnameError;
    // echo $fnameFlag;
    // echo '<hr>';

    // lastname

    if ( empty( $_POST[ 'lname' ] ) ) {
        $lnameError = 'lastname is required';
        $lnameFlag = false;
    } else if ( !checkAlphabhet( cleanData( $_POST[ 'lname' ] ) ) ) {
        $lnameError = 'only alphabhet allowed';
        $lnameFlag = false;
    } else if ( strlen( $_POST[ 'lname' ] ) < 3 ) {
        $lnameError = 'please enter at least 3 character lastname';
        $lnameFlag = false;
    } else {
        $lname = $_POST[ 'lname' ];
        //cho $lname;
    }

    // echo $lnameError;
    // echo $lnameFlag;
    // echo '<hr>';

    //email

    if ( empty( $_POST[ 'email' ] ) ) {
        $emailError = 'email is required';
        $emailFlag = false;
    } else if ( !filter_var ( $_POST[ 'email' ], FILTER_VALIDATE_EMAIL ) ) {
        $emailError = 'please enter a valid email address';
        $emailFlag = false;
    } else {
        $email = $_POST[ 'email' ];
        //echo $email;
    }

    // echo $emailError;
    // echo $emailFlag;
    // echo '<hr>';

    //email

    if ( empty( $_POST[ 'phone' ] ) ) {
        $phoneError = 'phone number is required';
        $phoneFlag = false;
    } else if ( !checkNumber( cleanData( $_POST[ 'phone' ] ) ) ) {

        $phoneError = 'only number allowed';
        $phoneFlag = false;

    } else if ( strlen( $_POST[ 'phone' ] ) > 10 ) {
        $phoneError = 'please enter no more than 10 characters';
        $phoneFlag = false;
    } else {
        $phone = $_POST[ 'phone' ];
        //echo $phone;
    }

    // echo $phoneError;
    // echo $phoneFlag;
    // echo '<hr>';

    //status

    if ( empty( $_POST[ 'status' ] ) ) {
        $stausError = 'status number is required';
        $stausFlag = false;
    } else if ( !checkAlphabhet( cleanData( $_POST[ 'status' ] ) ) ) {
        $stausError = 'only alphabhet allowed';
        $stausFlag = false;
    } else {
        $status = $_POST[ 'status' ];
        // echo $status;
    }

    // echo $stausError;
    // echo $stausFlag;
    // echo '<hr>';

    //gender

    if ( empty( $_POST[ 'gender' ] ) ) {
        $genderError = 'phone number is required';
        $genderflag = false;
    } else if ( !checkAlphabhet( cleanData( $_POST[ 'gender' ] ) ) ) {
        $genderError = 'only alphabhet allowed';
        $genderflag = false;
    } else {
        $gender = $_POST[ 'gender' ];
        // echo $gender;
    }

    // echo $genderError;
    // echo $genderflag;
    // echo '<hr>';

    $register_time = getCurrentTime();

    if ( $fnameFlag && $lnameFlag && $emailFlag && $phoneFlag && $stausFlag && $genderflag ) {
    try {

        $stmt = $connect->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            $email_exist=  "Email address exists in the database.";
        } else {

        $sql_insert_qry = "INSERT INTO `users`(`name`, `lastname`, `email`, `phone`, `status`, `gender`, `register_at`) VALUES 
        (?,?,?,?,?,?,?)";

        $statment = $connect->prepare( $sql_insert_qry );

        $register_time = getCurrentTime();
        $statment->execute( [ $fname, $lname, $email, $phone, $status, $gender, $register_time ] );

        //echo 'new record added';
        $_SESSION['msg'] = "new user added successfully";
        header("Location: list.php");
        }
    } catch( Exception $e ) {
        //echo 'Error : ' . $e->getMessage();
     
    }
    finally {
        $statment = null;
        $connect = null;

    }
}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add user</title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- bootstrap js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- jquery validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
<style>
    .error-border {
        border: 2px solid red !important;
    }

    .success-border {
        border: 2px solid green !important;
    }
</style>
</head>

<body>

    <div class="cotainer mt-5">
        <div class="d-flex d-flex align-items-center justify-content-center">
            <div class="card w-100 w-sm-75 shadow border-rounded mx-5">
                <div class="card-title text-center text-secondary fw-bold fs-4 mt-4">user information display</div>
                <div class="card-body">
                    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" id="formDataId" novalidate>
                        <div class="row g-4 mx-auto">
                            
                            <div class="col-12 col-md-6">
                                <label for="fname" class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control border-info" id="name" placeholder="Name" name="fname" value="<?php echo $fname; ?>">
                              
                                <label id="name-error" class="error text-danger" for="name"></label>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="lname" class="form-label">LastName <span
                                        class="text-danger">*</span></label>

                                <input type="text" name="lname" id="lname" class="form-control border-info"
                                    placeholder="Lastname" value="<?php echo $lname; ?>">
                                 <label id="lname-error" class="error text-danger" for="lname"></label>
                               
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control border-info"
                                    placeholder="Email" value="<?php  echo isset($email) ? "$email" : "";  ?>">
                               <label id="email-error" class="error text-danger" for="email"><?php  echo isset($emailExist) ? "$emailExist" : "";  ?></label>
                               <label id="email-errordb" class="error text-danger" for="emaildb"><?php  echo isset($email_exist) ? "$email_exist" : "";  ?></label>
                               
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone">Phone<span class="text-danger">*</span></label>
                                <input type="number" name="phone" id="phone" class="form-control border-info" placeholder="phone number" value="<?php echo $phone; ?>">
                                <label id="phone-error" class="error text-danger" for="phone"></label>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select border-info" placeholder="status" value="<?php echo $status; ?>">
                                    <option value="married">Married</option>
                                    <option value="unmarried">Unmarried</option>
                                </select>

                            </div>

                            <div class="col-12">
                                <label for="gender" class="form-label">Gender :</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-info" type="radio" name="gender" id="gender"
                                        value="male" checked>
                                    <label class="form-check-label" for="male">Male</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-info" type="radio" name="gender" id="gender"
                                        value="female">
                                    <label class="form-check-label" for="female">Femlae</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input border-info" type="radio" name="gender" id="gender"
                                        value="other">
                                    <label class="form-check-label" for="other">Other</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-center">
                                    <input type="submit" name="submit" value="Add" class="btn btn-dark" id="submitform">
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
           
            $("#formDataId").validate({
                onkeydown: function (element) {
                    let validator = this;
                    setTimeout(() => {
                        validator.element(element);
                    }, 500);
                },
                rules: {
                    fname: {
                        required: true,
                        minlength: 3,
                    },
                    lname: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: "./checkemail.php",
                            type: "post",
                            data: {
                                email: function () {
                                    return $("#email").val();
                                }
                            }
                        }
                    },
                   phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                }
                },
                messages: {
                    fname: {
                        required: "name is required",
                        minlength: "please enter at least 3 character"
                    },
                    lname: {
                        required: "lastname is required",
                        minlength: "please enter at least 3 character"
                    },
                    email: {
                        required: "email is required",
                        remote: "email already exist"
                    },
                    phone: {
                        required: "phone number is required",
                        minlength: "please enter at least 10 characters",
                        maxlength: "please enter no more than 10 characters"
                    }
                },
         highlight: function (element) {
        $(element)
            .removeClass("success-border")
            .addClass("error-border");
    },

    unhighlight: function (element) {
        $(element)
            .removeClass("error-border")
            .addClass("success-border");
    },
    
    submitHandler: function (form) {
        form.submit();
        },
    });
});

    </script>
</body>

</html>