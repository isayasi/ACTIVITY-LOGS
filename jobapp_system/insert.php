<?php require_once 'core/handleForms.php'; ?>
<?php require_once 'core/models.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<h1>Edit the user!</h1>
	<form action="core/handleForms.php" method="POST">
        <p>
			<label for="username">UserName</label> 
			<input type="text" name="username">
		</p>
		<p>
			<label for="firstName">First Name</label> 
			<input type="text" name="first_name">
		</p>
		<p>
			<label for="lastname">Last Name</label> 
			<input type="text" name="last_name">
		</p>
		<p>
			<label for="email">Email</label> 
			<input type="text" name="email">
		</p>
		<p>
			<label for="phone">Phone</label> 
			<input type="text" name="phone">
		</p>
		<p>
			<label for="address">Address</label> 
			<input type="text" name="address">
		</p>
		<p>
			<label for="position">Position</label> 
			<input type="text" name="position">
		</p>
		<p>
			<input type="submit" name="insertUserBtn">
		</p>
	</form>
</body>
</html>