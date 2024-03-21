<?php
if (isset($_POST["show"])) {
  // Basic requermentation
  $servername = $_REQUEST['server_name'];
  $username = $_REQUEST['user_name'];
  $password = $_REQUEST['password'];
  $database = $_REQUEST['database'];

  // excel data
  $file = $_FILES['csvfile']['tmp_name'];
  $tblname = $_REQUEST['tbl_name'];
  $cname = $_REQUEST['excel_column_name'];

  $sql_where = "";
  $where_col_name = "";
  $conditional_ope = "";
  $col_value = "";
  if (isset($_REQUEST['check_codition'])) {
    $sql_where = $_REQUEST['sql_where'];
    $where_col_name = $_REQUEST['where_col_name'];
    $conditional_ope = $_REQUEST['conditional_ope'];
    $col_value = $_REQUEST['col_value'];
  }

  // excel file settings
  $handle = fopen($file, "r");
  $count = 0;

  // Create connection
  $conn = new mysqli($servername, $username, $password, $database);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  while (($filesop = fgetcsv($handle, 1000, ",")) !== false) {
    $newcname = "";
    $updateData = "";
    $cvalue = "";
    for ($i = 0; $i < count($filesop); $i++) {
      if (count($cname) > 1) {
        if ($i == 0) {
          // Insert data setting
          $newcname = $cname[$i];
          $cvalue = "'" . $filesop[$i] . "'";

          // Update data setting
          if ($sql_where != "") {
            $updateData = $cname[$i] . "='" . $filesop[$i] . "'";
          }
        } else {
          // Insert data setting
          $newcname = $newcname . "," . $cname[$i];
          $cvalue = $cvalue . ",'" . $filesop[$i] . "'";

          // Update data setting
          if ($sql_where != "") {
            $updateData = $updateData . "," . $cname[$i] . "='" . $filesop[$i] . "'";
          }
        }
      } else {
        // Insert data setting
        $newcname = $cname[0];
        if (empty($cvalue)) {
          $cvalue = "'" . $filesop[$i] . "'";
        } else {
          $cvalue = $cvalue . ",'" . $filesop[$i] . "'";
        }

        // Update data setting
        if ($sql_where != "") {
          $updateData = $cname[0] . "='" . $filesop[$i] . "'";
        }
      }
    }

    if ($sql_where != "") {
      $wherValue = "";
      for ($i = 0; $i < count($sql_where); $i++) {
        $wherValue = $wherValue . " " . $sql_where[$i] . " " . $where_col_name[$i] . "" . $conditional_ope[$i] . "'" . $col_value[$i] . "'";
      }
      $sql = "UPDATE `$tblname` SET $updateData " . " $wherValue";
      echo "<br>=> " . $sql;
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_execute($stmt);
    } else {
      $sql = "INSERT INTO `$tblname`($newcname) VALUES ($cvalue)";
      echo "<br>=> " . $sql;
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_execute($stmt);
    }
  }
  fclose($handle);
  if ($sql) {
    echo '<div id="sucess">
      <strong>Sucessfully Saved,</strong>
      <strong class="text-danger">Please do not refresh the page otherwise it will again saved! </strong>
      <button type="button" class="btn btn-info btn-sm" onclick="history.back()">Back</button>
    </div>';
    echo '<script>
      setTimeout(successFunc, 3000);
      function successFunc(){
        alert("Excel Data Saved Successfully!!");
        // window.location=document.referrer;
      }
    </script>';
  } else {
    echo "<div id='false'>Sorry! Unable to impo.</div>";
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upload Excel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>
<style>
  #sucess {
    padding: 10px;
    margin: 10px;
    text-align: center;
    color: green;
    background-color: beige;
  }

  .container {
    box-shadow: rgba(50, 50, 93, 0.25) 0px 50px 100px -20px,
      rgba(0, 0, 0, 0.3) 0px 30px 60px -30px,
      rgba(10, 37, 64, 0.35) 0px -2px 6px 0px inset;
    border-radius: 10px;
    background-color: #EEEDED;
  }
</style>

<body>
  <div class="container">
    <div class="my-5 p-4">
      <form enctype="multipart/form-data" method="post" role="form">
        <div class="align-item-center">
          <div class="row">
            <div class="col-6 col-lg-4 form-group">
              <label for="server_name">Enter server name</label>
              <input type="text" name="server_name" value="localhost" id="server_name" class="form-control" placeholder="localhost/127.0.0.1" required />
            </div>
            <div class="col-6 col-lg-4 form-group">
              <label for="username">Enter database user name</label>
              <input type="text" name="user_name" value="root" id="user_name" class="form-control" placeholder="root" required />
            </div>
            <div class="col-6 col-lg-4 form-group">
              <label for="password">Enter database password</label>
              <input type="password" name="password" id="password" class="form-control" placeholder="****" />
            </div>
            <div class="col-6 col-lg-4 form-group">
              <label for="database">Enter database name</label>
              <input type="text" name="database" id="database" class="form-control" placeholder="polybond_database" required />
            </div>
            <!-- <div class="col-6 col-lg-4 form-group">
              <label for="database">Select Action</label>
              <select name="sql_action" id="sql_action" class="form-control" require>
                <option value="INSERT INTO">INSERT</option>
                <option value="UPDATE">UPDATE</option>
                <option value="DELETE FROM">DELETE</option>
              </select>
            </div> -->
            <div class="col-6 col-lg-4 form-group">
              <label for="tbl_name">Enter table name</label>
              <input type="text" name="tbl_name" id="tbl_name" class="form-control" placeholder="polybond_table" required />
            </div>
            <div class="col-6 col-lg-4 form-group">
              <label for="csvfile">Select CSV File</label>
              <input type="file" name="csvfile" id="csvfile" class="form-control" accept=".csv" required />
            </div>
          </div>
          <br>
          <div class="form-check">
            <input class="form-check-input" name="check_codition" type="checkbox" value="" id="check_codition">
            <label class="form-check-label" for="check_codition">Check, If you want to <u>update</u> table and add conditions</label>
          </div>
          <table id="where_tbl" class="table" style="display: none;">
            <thead>
              <tr>
                <td>Condition</td>
                <td>Column Name</td>
                <td>Conditional Operator</td>
                <td>Column Value</td>
                <td>Button</td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
          <br>
          <div class="">
            <div class=" text-danger text-center"><strong>Node: </strong> The column names should be in the same order as the columns in Excel.</div>
            <table class="table">
              <thead>
                <tr>
                  <td>Column No.</td>
                  <td>Column Name</td>
                  <td>Add button</td>
                  <td>Delete Button</td>
                </tr>
              </thead>
              <tbody id="excel_tbl_body">
              </tbody>
            </table>
          </div>
          <div class="form-group text-center">
            <button type="submit" name="show" id="excel_form_submit_btn" class="btn btn-primary" style="width:20%">Upload</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    var colRowNum = 0;
    var whereRowNum = 0;
    add_column();

    function delete_column(id) {
      document.getElementById("tblrow" + id).remove();
    }

    function delete_where_row(id) {
      document.getElementById("wherTblrow" + id).remove();
    }

    function add_column() {
      colRowNum++;
      var html = `
        <tr id="tblrow` + colRowNum + `">
          <td>` + colRowNum + `</td>
          <td><input type="text" name="excel_column_name[]" class="form-control" /></td>
          <td><button type="button" onclick="add_column()" class="btn btn-primary btn-sm">Add</button></td>
          <td><button type="button" onclick="delete_column(` + colRowNum + `)" class="btn btn-danger btn-sm">Delete</button></td>
        </tr>
      `;
      document.getElementById("excel_tbl_body").innerHTML += html;
    }

    // add conditions
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("check_codition").addEventListener("change", function() {
        whereRowNum = 0;
        let html = `<tr>
                <td>
                  <div class="form-group">
                    <input type="text" name="sql_where[]" id="sql_where" value="WHERE" class="form-control" required readonly/>
                  </div>
                </td>
                <td>
                  <div class="form-group">
                    <input type="text" name="where_col_name[]" id="where_col_name" class="form-control" placeholder="column name" required />
                  </div>
                </td>
                <td>
                  <div class="form-group">
                    <select name="conditional_ope[]" id="conditional_ope" class="form-control" require>
                      <option value="="> "=" Equal to </option>
                      <option value=">"> ">" Greater than </option>
                      <option value="<"> "<" Less than </option>
                      <option value=">="> ">=" Greater than or equal to </option>
                      <option value="<="> "<=" Less than or equal to </option>
                      <option value="!="> "!=" Not equal to </option>
                    </select>
                  </div>
                </td>
                <td>
                  <div class="form-group">
                    <input type="text" name="col_value[]" id="col_value" class="form-control" placeholder="column value" required />
                  </div>
                </td>
                <td><button type="button" onclick="add_condition_row()" class="btn btn-primary btn-sm">Add</button></td>
              </tr>`;
        if (this.checked) {
          document.getElementById("where_tbl").style.display = "block";
          document.getElementById("check_codition").value = "checked";
          document.querySelector("#where_tbl tbody").innerHTML = html;
        } else {
          document.getElementById("where_tbl").style.display = "none";
          document.getElementById("check_codition").value = "";
          document.querySelector("#where_tbl tbody").innerHTML = "";
        }
      });
    });

    // when clicking on the button add new condition rows
    function add_condition_row() {
      whereRowNum++;
      let html = `<tr id="wherTblrow` + whereRowNum + `">
              <td>
                <div class="form-group">
                  <select name="sql_where[]" id="sql_where" class="form-control" require>
                    <option value="AND">AND</option>
                    <option value="OR">OR</option>
                  </select>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <input type="text" name="where_col_name[]" id="where_col_name" class="form-control" placeholder="column name" required />
                </div>
              </td>
              <td>
                <div class="form-group">
                  <select name="conditional_ope[]" id="conditional_ope" class="form-control" require>
                    <option value="="> "=" Equal to </option>
                    <option value=">"> ">" Greater than </option>
                    <option value="<"> "<" Less than </option>
                    <option value=">="> ">=" Greater than or equal to </option>
                    <option value="<="> "<=" Less than or equal to </option>
                    <option value="!="> "!=" Not equal to </option>
                  </select>
                </div>
              </td>
              <td>
                <div class="form-group">
                <input type="text" name="col_value[]" id="col_value" class="form-control" placeholder="column value" required />
                </div>
              </td>
              <td><button type="button" onclick="add_condition_row()" class="btn btn-primary btn-sm">Add</button>
                <button type="button" onclick="delete_where_row(` + whereRowNum + `)" class="btn btn-danger btn-sm">Delete</button>
              </td>
            </tr>`;
      document.querySelector("#where_tbl tbody").innerHTML += html;
    }
  </script>
</body>

</html>