<?php
//Establishing Connection
$serverName = "localhost";
$userName = "root";
$password = "";
$dataBase = "todolist";

$conn = mysqli_connect($serverName, $userName, $password, $dataBase);

if (!$conn) {
  die("Sorry we failed to connect..." . mysqli_connect_error($conn));
}

$insert = false;
$update = false;
$delete = false;

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ToDo app</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <link rel="stylesheet" href="////cdn.datatables.net/2.0.5/css/dataTables.dataTables.min.css">

</head>

<body>

  <!-- Edit modal 
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
    Edit
  </button>
  -->

  <!-- Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="editModal">Edit Note</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/TODO LIST/index.php" method="post">
            <input type="hidden" name="snoEdit" id="snoEdit">
            <div class="mb-3 my-4">
              <label for="title" class="form-label">Note Title:</label>
              <input type="text" class="form-control" id="titleEdit" name="titleEdit" aria-describedby="emailHelp" />
            </div>
            <div class="mb-3">
              <label for="desc" class="form-label">Note Description:</label>
              <div class="form-floating">
                <textarea class="form-control" placeholder="Describe me..." id="descriptionEdit" name="descriptionEdit">
            </textarea>
                <label for="floatingTextarea" class="text-secondary">Describe me...</label>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Note</button>
          </form>
        </div>
      </div>
    </div>
  </div>



  <!--Navbar Starts-->
  <nav class="navbar navbar-expand-lg bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand text-light" href="#">PHP CRUD</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active text-light" aria-current="page" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#">About US</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#">Contact</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!--Navbar Ends-->


  <?php

  //delete note
  if (isset($_GET['delete'])) {
    $sno = $_GET['delete'];
    $delete = true;
    $sql = "DELETE FROM `notes` WHERE `sno` = $sno";
    $result = mysqli_query($conn, $sql);

    if ($result) {
      $delete = true;
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Hurray!!</strong> Your note has been deleted successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      ';
    } else {
      echo "Unable to delete note";
    }
  }


  //Sending data to DB
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['snoEdit'])) {
      //update note
      $sno = $_POST['snoEdit'];
      $title = $_POST['titleEdit'];
      $description = $_POST['descriptionEdit'];

      $sql = "UPDATE `notes` SET `title` = '$title', `description` = '$description' WHERE `notes`.`sno` = $sno";
      $result = mysqli_query($conn, $sql);

      if ($result) {
        $update = true;
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Hurray!!</strong>Your note has been updated successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        ';
      } else {
        echo "Unable to update note";
      }
    } else {
      // add note to DB
      $title = $_POST['title']; //fetching title of note from UI
      $description = $_POST['description']; //fetching description of note from UI
      $sql = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')"; //insertion query
      $result = mysqli_query($conn, $sql); //execution of query

      if ($result) {
        $insert = true;
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Hurray!!</strong>Your note has been added successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        ';
      } else {
        echo "Unable to add note";
      }
    }
  }
  ?>



  <!--Write Notes-->
  <div class="container my-4">
    <h2>Write your Note</h2>
    <form action="/TODO LIST/index.php" method="post">
      <div class="mb-3 my-4">
        <label for="title" class="form-label">Note Title:</label>
        <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp" />
      </div>
      <div class="mb-3">
        <label for="desc" class="form-label">Note Description:</label>
        <div class="form-floating">
          <textarea class="form-control" placeholder="Describe me..." id="desc" name="description">
            </textarea>
          <label for="floatingTextarea" class="text-secondary">Describe me...</label>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Add Note</button>
    </form>
  </div>


  <hr>


  <!--Show Notes DB-->
  <div class="container my-4">
    <h2>Your Notes</h2>

    <table class="table table-striped my-4" style="width:100%" id="myTable">
      <thead>
        <tr>
          <th scope="col">S. no</th>
          <th scope="col">Title</th>
          <th scope="col">Description</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php

        $sql = "SELECT * from `notes`";
        $result = mysqli_query($conn, $sql);
        $sno = 0; //for serial number of notes
        while ($row = mysqli_fetch_assoc($result)) {
          $sno = $sno + 1;
          echo "<tr>
          <th scope='row'>" . $sno . "</th>
          <td>" . $row['title'] . "</td>
          <td>" . $row['description'] . "</td>
          <td><button type='button' class='btn btn-sm edit btn-primary' id=" . $row['sno'] . " data-bs-toggle='modal' data-bs-target='#editModal'>Edit</button>
          <button type='button' class='delete btn btn-sm edit btn-danger' id=d" . $row['sno'] . " data-bs-toggle='modal' data-bs-target='#deleteModal'>Delete</button>
          </td>
        </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

<!-- data table logic-->
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="//cdn.datatables.net/2.0.5/js/dataTables.min.js"></script>
<script>
  let table = new DataTable('#myTable');
</script>

<script>
  //Fetching note for edit(JS logic)
  edits = document.getElementsByClassName('edit');
  Array.from(edits).forEach((element) => {
    element.addEventListener("click", (e) => {
      console.log("edit ", e.target.parentNode.parentNode);
      tr = e.target.parentNode.parentNode;
      title = tr.getElementsByTagName("td")[0].innerText;
      description = tr.getElementsByTagName("td")[1].innerText;
      console.log(title, description);
      titleEdit.value = title;
      descriptionEdit.value = description;
      snoEdit.value = e.target.id;
      console.log(snoEdit);
    })
  })


  //delete JS logic 
  deletes = document.getElementsByClassName('delete');
  Array.from(deletes).forEach((element) => {
    element.addEventListener("click", (e) => {
      console.log("delete ", e.target.parentNode.parentNode);
      sno = e.target.id.substr(1, );

      if (confirm("Do yo want to delete this note?")) {
        console.log('yes');
        window.location = `/ToDo List/index.php?delete=${sno}`;
      } else {
        console.log('no');
      }

    })
  })
</script>

</html>