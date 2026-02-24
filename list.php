<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include './db.php';

try {

    $columns = ['id', 'name', 'lastname', 'email', 'phone', 'status', 'gender', 'register_at'];

    $sort  = $_GET['sort']  ?? 'id';
    $order = $_GET['order'] ?? 'asc';

    if (!in_array($sort, $columns)) {
        $sort = 'id';
    }

    $order = strtolower($order) === 'desc' ? 'desc' : 'asc';

    // Search
    $search = $_GET['search'] ?? '';
    $search_item = '%' . $search . '%';

    // Count with search
    $count_sql = "SELECT COUNT(*) as total_records 
                  FROM users 
                  WHERE CONCAT(name,' ',lastname,' ',email) LIKE :search";

    $stmt = $connect->prepare($count_sql);
    $stmt->bindValue(':search', $search_item, PDO::PARAM_STR);
    $stmt->execute();
    $total_records = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total_records'];

    // Per page
    $per_page = $_GET['per_page'] ?? 10;

    if ($per_page === 'all') {
        $per_page = $total_records ?: 1;
    } else {
        $per_page = (int)$per_page;
        if ($per_page <= 0) $per_page = 10;
    }

    // Pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = ($page < 1) ? 1 : $page;

    $total_pages = ($total_records > 0) ? ceil($total_records / $per_page) : 1;

    if ($page > $total_pages) {
        $page = $total_pages;
    }

    $offset = ($page - 1) * $per_page;

    // Final Query
    $sql = "SELECT * FROM users
            WHERE CONCAT(name,' ',lastname,' ',email) LIKE :search
            ORDER BY $sort $order
            LIMIT :offset, :per_page";

    $stmt = $connect->prepare($sql);
    $stmt->bindValue(':search', $search_item, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    $stmt->execute();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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

    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    
</head>

<body>

<div class = 'container mt-5'>

     <?php if (!empty($_SESSION['msg'])): ?>
        <div id="msg" class="alert alert-success">
            <?= htmlspecialchars($_SESSION['msg']); ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>


    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid">

            <button class = 'btn btn-warning'>
                <a href = './add.php' class = 'text-decoration-none text-light'> Add user</a>
            </button>

              <ul class='justify-content-start'>

              
                <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Record per page
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item"
                                href="?per_page=5&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&page=1">
                                5
                                </a>
                            </li>

                        <li>
                            <a class="dropdown-item"
                            href="?per_page=10&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&page=1">
                            10
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                            href="?per_page=15&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&page=1">
                            15
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item"
                            href="?per_page=all&search=<?= htmlspecialchars($_GET['search'] ?? '') ?>&page=1">
                            All
                            </a>
                        </li>      
                    </ul>
                    </div>
       
     
    </ul>


            <form method="GET" action="<?= $_SERVER['PHP_SELF'] ?>" class="d-flex">
                <input class="form-control me-2" type="text" name="search"
                    value="<?= htmlspecialchars($search) ?>" placeholder="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <hr>

     <div class='table-responsive'>
        <table class = 'table'>
            <thead>
                <tr>
                    <th>
                         <?php
                            $newOrder = ($sort == 'id' && $order == 'asc') ? 'desc' : 'asc';
                            $arrow = ($sort == 'id') ? ($order == 'asc' ? '▲' : '▼') : '';
                        ?>
                        <a class="text-dark text-decoration-none"
                        href="?per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&sort=id&order=<?= $newOrder ?>&page=<?= $page ?>">
                            ID <?= $arrow ?>
                        </a>
                    </th>
              
                    <th>
                        <?php
                            $newOrder = ($sort == 'name' && $order == 'asc') ? 'desc' : 'asc';
                            $arrow = ($sort == 'name') ? ($order == 'asc' ? '▲' : '▼') : '';
                        ?>
                        <a class="text-dark text-decoration-none"
                        href="?per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&sort=name&order=<?= $newOrder ?>&page=<?= $page ?>">
                            First Name <?= $arrow ?>
                        </a>
                    </th>
                    <th>
                         <?php
                            $newOrder = ($sort == 'lastname' && $order == 'asc') ? 'desc' : 'asc';
                            $arrow = ($sort == 'lastname') ? ($order == 'asc' ? '▲' : '▼') : '';
                        ?>
                        <a class="text-dark text-decoration-none"
                        href="?per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&sort=lastname&order=<?= $newOrder ?>&page=<?= $page ?>">
                            Lastname <?= $arrow ?>
                        </a>
                    </th>
                      <th>
                         <?php
                            $newOrder = ($sort == 'email' && $order == 'asc') ? 'desc' : 'asc';
                            $arrow = ($sort == 'email') ? ($order == 'asc' ? '▲' : '▼') : '';
                        ?>
                        <a class="text-dark text-decoration-none"
                        href="?per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&sort=email&order=<?= $newOrder ?>&page=<?= $page ?>">
                             
                            Email <?= $arrow ?>
                        </a>
                    </th>
                    <th>
                         <?php
                            $newOrder = ($sort == 'phone' && $order == 'asc') ? 'desc' : 'asc';
                            $arrow = ($sort == 'phone') ? ($order == 'asc' ? '▲' : '▼') : '';
                        ?>
                        <a class="text-dark text-decoration-none"
                            href="?per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&sort=phone&order=<?= $newOrder ?>&page=<?= $page ?>">
                            Phone <?= $arrow ?>
                        </a>
                    </th>
                    <th scope = 'col'>Status</th>
                    <th scope = 'col'>Gender</th>
                
                    <th>
                         <?php
                            $newOrder = ($sort == 'register_at' && $order == 'asc') ? 'desc' : 'asc';
                            $arrow = ($sort == 'register_at') ? ($order == 'asc' ? '▲' : '▼') : '';
                        ?>
                        <a class="text-dark text-decoration-none"
                        href="?per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&sort=register_at&order=<?= $newOrder ?>&page=<?= $page ?>">
                            Register time <?= $arrow ?>
                        </a>
                    </th>
                    <th>
                        Action
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
                    echo '<td>
                            <a href="./edit.php?id=' . $row['id'] . '" 
                            class="text-decoration-none text-light mx-2 btn btn-warning me-2">
                            Edit
                            </a>
                        </td>';                   
                    echo "<td>
                            <a href='./delete.php?id={$row['id']}'
                            class='text-decoration-none text-light mx-2 btn btn-danger me-2'
                            onclick=\"return confirm('Are you sure you want to delete this item?');\">
                            Delete
                            </a>
                        </td>";
                    echo '</tr>';
                }
            ?>

         </table>
    </div>

   <nav class="mt-4">
    <ul class="pagination justify-content-center">

        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link"
                   href="?&per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&page=<?= $page - 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= $_GET['search']?>">
                    Previous
                </a>
            </li>
        <?php endif; ?>


        <?php
       
            $range = 2; 

            $start = max(1, $page - $range);
            $end   = min($total_pages, $page + $range);

            for ($i = $start; $i <= $end; $i++):
        ?>
            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                <a class="page-link"
                   href="?&per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>



        <?php if ($page < $total_pages): ?>
            <li class="page-item">
               <a class='page-link' 
               href="?&per_page=<?= htmlspecialchars($_GET['per_page'] ?? '10') ?>&page=<?= $page + 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&search=<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                Next
            </a>

            </li>
        <?php endif; ?>

    </ul>

  
</nav>


</div>

 <script>

         $(document).ready(function(){
                
                // msg timeout
            setTimeout(function(){
                $("#msg").fadeOut("fast");
            }, 500);

            $("#delete").click(function(){
                confirm("are you sure to delete this record");
            });
               
        });
    </script>
</body>

</html>