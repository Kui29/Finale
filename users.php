<?php
require_once("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stdId = $_POST["stdId"];
    $stdName = $_POST["stdName"];
    $stdGender = $_POST["stdGender"];
    $stdEmail = $_POST["stdEmail"];
    $stdContact = $_POST["stdContact"];
    $stdPassword = hash('sha256', $_POST["stdPassword"]);

    $sql = "INSERT INTO Students (stdId, stdName, stdGender, stdEmail, stdContact, stdPassword)
        VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            stdName = VALUES(stdName),
            stdGender = VALUES(stdGender),
            stdEmail = VALUES(stdEmail),
            stdContact = VALUES(stdContact),
            stdPassword = VALUES(stdPassword)";

    $stmt_insert = $con->prepare($sql);
    $stmt_insert->bind_param("isssss", $stdId, $stdName, $stdGender, $stdEmail, $stdContact, $stdPassword);
    if ($stmt_insert->execute()) {
        if ($stmt_insert->affected_rows === 1) {
            $_SESSION['success'] = 'Student added successfully!.';
        } elseif ($stmt_insert->affected_rows > 1) {
            $_SESSION['success'] = 'Student updated successfully!';
        }
    } else {
        $_SESSION['error'] = 'Error: ' . $con->error;
    }

    $stmt_insert->close();
}

$stmt = $con->prepare("SELECT * FROM Students");
$stmt->execute();       
$result = $stmt->get_result();
 ?>

<!-- HTML 5 -->
<!DOCTYPE html>
<html>
<head>
    <title>Student users</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style2.css"> <!-- Link to external CSS file -->
    <script>
function validateForm(formId) {
    //.trim ensures that the input value does not have a whitespace.
    let form = document.getElementById(formId);
    let stdId = form.stdId.value.trim();
    let stdName = form.stdName.value.trim();
    let stdGender = form.stdGender.value.trim();
    let stdEmail = form.stdEmail.value.trim();
    let stdContact = form.stdContact.value.trim();
    let stdPassword = form.stdPassword.value.trim();
    let stdPasswordConf = form.stdPasswordConf.value.trim();
    
    // Check if fields are empty
    //=== is strict comparison operator
    if (stdId === "" || stdName === "" || !stdGender || stdEmail === "" || stdContact === "") {
        alert("All fields are required.");
        return false;
    }

    // Validate Student ID (only numbers)
    if (isNaN(stdId)) {
        alert("Student ID must contain only numbers.");
        return false;
    }

    // Validate Name (should allow letters, spaces, and special characters, but no numbers)
    stdName = stdName.split(""); // Convert string to an array of characters
    for (let i = 0; i < stdName.length; i++) {
        let char = stdName[i];

        // Check if the character is a number
        if (!isNaN(char) && char !== " ") {
            alert("Name cannot contain numbers.");
            return false;
        }
    }

    // Validate Email Format (basic check without regex)
    if (stdEmail.indexOf("@") === -1 || stdEmail.indexOf(".") === -1 || stdEmail.indexOf("@") > stdEmail.lastIndexOf(".")) {
        alert("Invalid email format.");
        return false;
    }

    // Validate Contact Number (only numbers, exactly 10 digits)
    if (isNaN(stdContact) || stdContact.length !== 10) {
        alert("Contact number must be exactly 10 digits and contain only numbers.");
        return false;
    }

    if (stdPassword === "") {
        alert("Password is required.");
        return false;
    }

    if (stdPassword.length < 8) {
        alert("Password must be at least 8 characters long.");
        return false;
    }
    if (stdPasswordConf !== stdPassword) {
        alert("Does not match password.");
        return false;
    }
    return true; // Allow form submission if all validations pass
}

    </script>
</head>
<body>
<div class="header">
    <h1>Students</h1>
</div>

<div class="main-container">

<?php
    if(isset($_SESSION['error'])){
        echo '<div class="alert error">
                <p>' . $_SESSION['error'] . '</p>
                <span class="close">&times;</span>
             </div>';
    }

    if(isset($_SESSION['success'])){
        echo '<div class="alert success">
                <p>' . $_SESSION['success'] . '</p>
                <span class="close">&times;</span>
        </div>';
    }

    unset($_SESSION['success']);
    unset($_SESSION['error']);
?>

    <div class="form-container">
        <h2>Add Student</h2>

<form id="studentsForm" action="users.php" method="POST" onsubmit="return validateForm('studentsForm')" enctype="application/x-www-form-urlencoded" >
    <div class="form-input">
        <label for="stdId">Student ID:</label>
        <input type="text" name="stdId" id="stdId">
    </div>
    <div class="form-input">
        <label for="stdName">Name:</label>
        <input type="text" name="stdName" id="stdName">
    </div>
    <div class="form-input">
        <label for="stdGenderMale">Gender:</label>
        <input type="radio" id="stdGenderMale" name="stdGender" value="Male">
        <label for="stdGenderMale" style="display:inline; margin-right:10px;">Male</label>
        <input type="radio" id="stdGenderFemale" name="stdGender" value="Female">
        <label for="stdGenderFemale" style="display:inline;">Female</label>
    </div>
    <div class="form-input">
        <label for="stdEmail">Email:</label>
        <input type="text" name="stdEmail" id="stdEmail">
    </div>
    <div class="form-input">
        <label for="stdContact">Contact:</label>
        <input type="text" name="stdContact" id="stdContact">
    </div>
    <div class="form-input">
        <label for="stdPassword">Password:</label>
        <input type="password" name="stdPassword" id="stdPassword">
    </div>
    <div class="form-input">
        <label for="stdPasswordConf">Password Confirmation:</label>
        <input type="password" name="stdPasswordConf" id="stdPasswordConf">
    </div>
    <div class="form-input">
        <input type="submit" name="Add" id="Add" value="Add">
    </div>
</form>
</div>

    <div class="table-container">
        <h2>Student List</h2>
        <table id="students-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody id="students-table-body">
                <?php
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['stdId']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stdName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stdGender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stdEmail']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stdContact']) . "</td>";
                        echo "<td>
    <div class='action-buttons'>
        <div class='edit-student-button'>
            <a href='edit_user.php?id=" . htmlspecialchars($row['stdId']) . "'>Edit</a>
        </div>
        <form action='delete_user.php' method='POST'>
            <input type='hidden' name='stdId' value='" . htmlspecialchars($row['stdId']) . "'>
            <input type='submit' class='remove-student-button' data-student-id='" . htmlspecialchars($row['stdId']) . "' value='Delete'>
        </form>
    </div>
</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <br><br><br><br><br><br>
    </div>

</div>

<script>
// c
document.querySelectorAll('.remove-student-button').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        const studentId = this.dataset.studentId;
        if (confirm(`Are you sure you want to delete student with ID ${studentId}?`)) {
            fetch('delete_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `stdId=${encodeURIComponent(studentId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Student deleted successfully.');
                    // Remove the row from the table
                    this.closest('tr').remove();
                } else {
                    alert('Error deleting student: ' + (data.message || 'Unknown error.'));
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
        }
    });
});
</script>

<script>
  document.querySelectorAll('.alert .close').forEach(closeButton => {

    closeButton.addEventListener('click', function() {
        this.parentElement.style.display = 'none';
    });
  });  
    
</script>

</body>
</html>