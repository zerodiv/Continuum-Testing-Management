<?php 
if ( is_array( $_POST['selectionBox'] ) ) {
?>
<html>
<head>
<title>Test - Results</title>
</head>
<body>
values: <br/><?php 
if ( is_array( $_POST['selectionBox'] ) && count($_POST['selectionBox']) > 0 ) {
   foreach ($_POST['selectionBox'] as $action_value ) {
      echo "Value: $action_value<br>\n";
   }
} else {
   echo 'Nothing selected';
}
?>
</body>
</html>
<?php } else { ?>
<html>
<head>
<title>Test</title>
</head>
<body>
<form action="/regression/06-Click/01-Click.php" method="POST">
<select multiple name="selectionBox[]">
<option value="1">Option 1</option>
<option value="2">Option 2</option>
<option value="3">Option 3</option>
<option value="4">Option 4</option>
</select>
<input type="submit">
</form>
</body>
</html>
<?php } ?>
