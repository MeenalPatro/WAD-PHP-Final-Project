<?php

include 'config.php';
$query = mysqli_query($conn, "SELECT * FROM users");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crud Operation using PHP and Mysql</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <div class="container">
       <h1> User List</h1>
       <a href="add.php">Add User</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
        <?php
        $no = 1;
        while ($user = mysqli_fetch_assoc($query)) :
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $user['name']; ?></td>   
            <td><?php echo $user['email']; ?></td>
            <td><?php echo $user['phone']; ?></td>
            <td><?php echo $user['address']; ?></td>
            <td>    
                <a href="edit.php?id=<?php echo $user['id']; ?>">Edit</a>
                <a href="action.php?id=<?php echo $user['id']; ?>&action=delete" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>