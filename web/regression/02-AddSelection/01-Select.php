<?php 
if ( is_array( $_POST['selectionBox'] ) ) {
?>
<html>
<head>
<title>Selectbox Test - Results</title>
</head>
<body>
Selectbox values: <br/><?php 
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
<title>Selectbox Test</title>
</head>
<body>
<form action="/regression/03-Select/01-Select.php" method="POST">
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
