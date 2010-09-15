<?php 
if ( isset( $_POST['action'] ) && $_POST['action'] == 'post' ) {
?>
<html>
<head>
<title>Test - Results</title>
</head>
<body>
Values: <br/><?php 
   echo "staticStore: " . $_POST['staticStore'] . "<br>\n";
   echo "javascriptStore: " . $_POST['javascriptStore'] . "<br>\n";
?>
</body>
</html>
<?php } else { ?>
<html>
<head>
<title>Test</title>
</head>
<body>
<form action="/regression/09-Store/01-Store.php" method="POST">
<input type="hidden" name="action" value="post">
<input type="text" size="25" name="staticStore" value="">
<input type="text" size="25" name="javascriptStore" value="">
<input type="submit" id="go">
</form>
</body>
</html>
<?php } ?>
