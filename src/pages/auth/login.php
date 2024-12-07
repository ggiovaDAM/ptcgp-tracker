<?php
require_once __DIR__ . "/../../backend/php/functions.php";

$result = -1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    require_once __DIR__ . "/../../backend/pdo/db.php";
    $db = connectToDatabase( __DIR__ . "/../../backend/pdo/server_config.xml");

    $sql = "SELECT * FROM users WHERE email = :email";
    $preparada = $db->prepare($sql);
    $preparada->execute([
        ":email" => $email
    ]);

    if ($preparada->rowCount() == 0) {
        $result = 1;
    } else {
        $userInfo = $preparada->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $userInfo["password"])) {
            $result = 1;
        } else {
            $result = 0;

            startSession();

            $_SESSION["LOGIN_ID"] = $userInfo["userID"];
            $_SESSION["LOGIN_EMAIL"] = $email;

            // todo: enviar al index
            header("Location: index.php");
            exit();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <main>
        <h1>Login</h1>
        <form id="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" target="_self">
            <label for="email">Email</label>
            <input type="email" name="email" placeholder="Email" required>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">

            <?php if ($result == 1) { ?>
                <p>Invalid credentials</p>
            <?php } ?>
        </form>
    </main>
</body>
</html>