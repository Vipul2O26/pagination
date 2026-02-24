<?php

ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

session_start();

include './db.php';

try {

    // filtering
    $sort = $_GET[ 'sort' ] ?? 'id';
    $order = $_GET[ 'order' ] ?? 'asc';

    // pagination
    $page = 1;

    if ( isset( $_GET[ 'page' ] ) ) {
        $page = filter_var( $_GET[ 'page' ], FILTER_SANITIZE_NUMBER_INT );
    }

    $per_page = $_GET['per_page'] ?? 3;

    $sqlcount = 'select count(*) as total_records from users';
    $stmt = $connect->prepare( $sqlcount );
    $stmt->execute();
    $row = $stmt->fetch();
    $total_records = $row[ 'total_records' ];

    $total_pages = ceil( $total_records / $per_page );

    $offset = ( $page-1 ) * $per_page;


    $sql = 'SELECT * FROM users ORDER BY ' . $sort . ' ' . $order . ' LIMIT :offset, :per_page';

    $stmt = $connect->prepare( $sql );
    $stmt->execute( [ 'offset'=>$offset, 'per_page'=>$per_page ] );

    

} catch( PDOException $e ) {
    echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang = 'en'>

<head>
    <meta charset = 'UTF-8'>
    <meta name = 'viewport' content = 'width=device-width, initial-scale=1.0'>
    <title>user list</title>

    <!-- bootstrap -->
    <link href = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css' rel = 'stylesheet'
    integrity = 'sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB' crossorigin = 'anonymous'>

    <!-- bootstrap js -->
    <script src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js'
    integrity = 'sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI'
    crossorigin = 'anonymous'></script>
</head>

<body>

<div class = 'container mt-5'>

    <?php
        echo $_SESSION['msg'];
    ?>

    <button class = 'btn btn-warning'>
        <a href = './add.php' class = 'text-decoration-none text-light'> Add user</a>
    </button>

     <div class='table-responsive'>
        <table class = 'table'>
            <thead>
                <tr>
                    <th scope = 'col'>Id
                         <a href='?sort=id&order=asc' class='text-decoration-none'>▲</a>
                        <a href='?sort=id&order=desc' class='text-decoration-none'>▼</a>
                    </th>
                    <th scope = 'col'>FirstName
                        <a href='?sort=name&order=asc' class='text-decoration-none'>▲</a>
                        <a href='?sort=name&order=desc' class='text-decoration-none'>▼</a>
                    </th>
                    <th scope = 'col'>LastName</th>
                    <th scope = 'col'>Email</th>
                    <th scope = 'col'>Phone</th>
                    <th scope = 'col'>Status</th>
                    <th scope = 'col'>Gender</th>
                    <th scope = 'col'>Register time
                        <a href='?sort=register_at&order=asc' class='text-decoration-none'>▲</a>
                        <a href='?sort=register_at&order=desc' class='text-decoration-none'>▼</a>
                    </th>
                </tr>
            </thead>

            <?php
                 while ( ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) !== false ) {
                    echo '<tr>';
                    echo '<td>' .$row[ 'id' ]. '</td>';
                    echo '<td>' .$row[ 'name' ]. '</td>';
                    echo '<td>' .$row[ 'lastname' ]. '</td>';
                    echo '<td>' .$row[ 'email' ]. '</td>';
                    echo '<td>' .$row[ 'phone' ]. '</td>';
                    echo '<td>' .$row[ 'status' ]. '</td>';
                    echo '<td>' .$row[ 'gender' ]. '</td>';
                    echo '<td>' .$row[ 'register_at' ]. '</td>';
                    echo '</tr>';
                }
            ?>

         </table>
    </div>

    <div class='d-flex'>
        <div class='text-start me-2'>
            <?php
                if ( isset( $page ) && $page - 1 >= 1 ) {
                    $prevPage = $page - 1;
                    echo '<a class="btn btn-dark" href="' . htmlspecialchars( $_SERVER[ 'PHP_SELF' ] . '?page=' . $prevPage, ENT_QUOTES, 'UTF-8' ) . '">Previous</a>';
                }

                    $prevPage = $page + 1;
                    echo '<a class="btn btn-info text-light" href="' . htmlspecialchars( $_SERVER[ 'PHP_SELF' ] . '?page=' . $prevPage, ENT_QUOTES, 'UTF-8' ) . '">1</a>';

                    $prevPage = $page + 1;
                    echo '<a class="btn btn-info text-light" href="' . htmlspecialchars( $_SERVER[ 'PHP_SELF' ] . '?page=' . $prevPage, ENT_QUOTES, 'UTF-8' ) . '">2</a>';
        
                    $prevPage = $page + 1;
                    echo '<a class="btn btn-info text-light" href="' . htmlspecialchars( $_SERVER[ 'PHP_SELF' ] . '?page=' . $prevPage, ENT_QUOTES, 'UTF-8' ) . '">3</a>';
        
                    $prevPage = $page + 1;
                    echo '<a class="btn btn-info text-light" href="' . htmlspecialchars( $_SERVER[ 'PHP_SELF' ] . '?page=' . $prevPage, ENT_QUOTES, 'UTF-8' ) . '">4</a>';
        
                    $prevPage = $page + 1;
                    echo '<a class="btn btn-info text-light" href="' . htmlspecialchars( $_SERVER[ 'PHP_SELF' ] . '?page=' . $prevPage, ENT_QUOTES, 'UTF-8' ) . '">5</a>';
        

                if ( $page + 1 <= $total_pages ) {
                    echo '<a class="btn btn-success" href="' . htmlspecialchars( $_SERVER[ 'PHP_SELF' ] . '?page=' . ( $page + 1 ), ENT_QUOTES, 'UTF-8' ) . '">Next</a>';
                }

            ?>
        </div>
    </div>

</div>
</body>

</html>