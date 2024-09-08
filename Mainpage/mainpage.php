<?php
    include 'connection.php';
    
    session_start();
    session_unset();

    setcookie("Duel_ID", "", time()-86400, '/');
    unset($_COOKIE['Duel_ID']);

    $IPaddress = $_SERVER['REMOTE_ADDR'];
    $IPquery = mysqli_query($conn, "SELECT * FROM `ip addresses` WHERE `IP address`= '$IPaddress'");  
    
    if (mysqli_num_rows($IPquery) > 0 && mysqli_fetch_assoc($IPquery)['Status'] == "Deactive")
        header("Location: /PHP/TicTacToe/AccessDenied/ipBlocked.html");
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST["p1-name"] && $_POST["p1-password"]) && ($_POST["p2-name"] && $_POST["p2-password"])) {
        $P1name = ucfirst($_POST["p1-name"]);
        $P1password = $_POST["p1-password"];

        $P2name = ucfirst($_POST["p2-name"]);
        $P2password = $_POST["p2-password"];

        function LastDate() { 
            date_default_timezone_set("Asia/Baku");

            $Date = date("Y-m-d H:i:s");

            return $Date;
        }

        $LastPlayed = LastDate();

        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `admins` WHERE `Name`='$P1name' AND `Password`='$P2name'")) > 0) {
            $AdminName = $P1name;
            $Password = $P2name;

            if (mysqli_fetch_assoc(($conn->query("SELECT * FROM `admins` WHERE `Name`= '$AdminName' AND `Password`= '$Password'")))['Status'] == 'Active') {
                $Admin_ID = mysqli_fetch_assoc(($conn->query("SELECT * FROM `admins` WHERE `Name`= '$AdminName'")))['ID']; 
                $_SESSION['adminID'] = $Admin_ID;
                unset($_SESSION['hasRunOnce']);
                echo "<script> window.location.href = '/PHP/TicTacToe/AdminPanel/Admins/adminsTable.php' </script>"; 
            }
            else
                echo "<script> alert('Admin $AdminName has been deactived !') </script>";
        }
        else {
            if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'")))['Password'] !== $P1password) || (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name'")))['Password'] !== $P2password)) {
                if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'")))['Password'] !== $P1password)
                    echo "<script> alert(`$P1name's name is already taken or $P1name has used wrong password !`) </script>";
                else 
                    echo "<script> alert(`$P2name's name is already taken or $P2name has used wrong password !`) </script>";
            }
            else {    
                if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")))['Status'] == "Deactive") && 
                    (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")))['Status'] == "Deactive")) {
                    echo "<script> alert('The Players $P1name and $P2name have been banned !') </script>";
                }
                else if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")))['Status'] == "Deactive") || 
                (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")))['Status'] == "Deactive")) {
                    echo (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 && mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")))['Status'] == "Deactive") ? 
                    "<script> alert('The Player $P1name has been banned !') </script>" : "<script> alert('The Player $P2name has been banned !') </script>";
                }
                else { 
                    if (mysqli_num_rows($IPquery) == 0 ) {
                        $IPdata = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $IPaddress));
    
                        if ($IPdata && $IPdata->geoplugin_city) {
                            $Location = $IPdata->geoplugin_city . ", " . $IPdata->geoplugin_countryName;
                            mysqli_query($conn, "INSERT INTO `ip addresses` (`IP address`, `Location`) VALUES ('$IPaddress', '$Location')");
                        }
                        else
                            mysqli_query($conn, "INSERT INTO `ip addresses` (`IP address`) VALUES ('$IPaddress')");

                        $IPaddress_ID = mysqli_fetch_assoc(($conn->query("SELECT * FROM `ip addresses` WHERE `IP address`= '$IPaddress'")))['ID'];
                    }
                    else
                        $IPaddress_ID = mysqli_fetch_assoc(($conn->query("SELECT * FROM `ip addresses` WHERE `IP address`= '$IPaddress'")))['ID'];

                    $LastPlayed = LastDate();     

                        // IF BOTH PLAYERS ARE OLD PLAYERS
                    if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 && mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0) {        
                        $P1_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'"))['ID'];
                        $P2_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name'"))['ID'];

                        mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$LastPlayed', `IP address ID` = '$IPaddress_ID' WHERE `ID` = '$P1_ID'");
                        mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$LastPlayed', `IP address ID` = '$IPaddress_ID' WHERE `ID` = '$P2_ID'");

                        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `duels` WHERE `P1 ID`= '$P1_ID' AND `P2 ID` = '$P2_ID'")) > 0 || mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `duels` WHERE `P1 ID`= '$P2_ID' AND `P2 ID` = '$P1_ID'")) > 0) {
                            mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `duels` WHERE `P1 ID`= '$P1_ID' AND `P2 ID` = '$P2_ID'")) > 0 ? 
                            $Duel_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `duels` WHERE `P1 ID`= '$P1_ID' AND `P2 ID`= '$P2_ID'"))['ID'] :  $Duel_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `duels` WHERE `P1 ID`= '$P2_ID' AND `P2 ID`= '$P1_ID'"))['ID'];
                            mysqli_query($conn, "UPDATE `duels` SET `Last Played` = '$LastPlayed' WHERE `ID` = '$Duel_ID'"); 
                        }
                        else {
                            mysqli_query($conn, "INSERT INTO `duels` (`P1 ID`, `P2 ID`, `Last Played`) VALUES ('$P1_ID', '$P2_ID', '$LastPlayed')"); 
                            $Duel_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `duels` WHERE `P1 ID`= '$P1_ID' AND `P2 ID`= '$P2_ID'"))['ID'];
                        }

                        $_SESSION['first_time?'] = false;
                    } 
                        // IF ONE OF THE PLAYERS IS AN OLD PLAYER
                    else if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0 || mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P2name' AND `Password` = '$P2password'")) > 0) {
                        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `players` WHERE `Name`= '$P1name' AND `Password` = '$P1password'")) > 0) {
                            $P1_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'"))['ID'];
        
                            mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$LastPlayed', `IP address ID` = '$IPaddress_ID' WHERE `ID` = '$P1_ID'");
                            mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address ID`) VALUES ('$P2name', '$P2password','$LastPlayed', '$IPaddress_ID')");

                            $P2_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name'"))['ID'];
                        }
                        else {
                            $P2_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name'"))['ID'];
        
                            mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$LastPlayed', `IP address ID` = '$IPaddress_ID' WHERE `ID` = '$P2_ID'");
                            mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address ID`) VALUES ('$P1name', '$P1password','$LastPlayed', '$IPaddress_ID')");

                            $P1_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'"))['ID'];
                        }
        
                        mysqli_query($conn, "INSERT INTO `duels` (`P1 ID`, `P2 ID`, `Last Played`) VALUES ('$P1_ID', '$P2_ID', '$LastPlayed')");

                        $Duel_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `duels` WHERE `P1 ID`= '$P1_ID' AND `P2 ID`= '$P2_ID'"))['ID'];
 
                        $_SESSION['first_time?'] = true;
                    }
                        // IF NONE PLAYERS ARE OLD PLAYERS
                    else {
                        mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address ID`) VALUES ('$P1name', '$P1password','$LastPlayed', '$IPaddress_ID')");
                        mysqli_query($conn, "INSERT INTO `players` (`Name`, `Password`, `Last Played`, `IP address ID`) VALUES ('$P2name', '$P2password','$LastPlayed', '$IPaddress_ID')");

                        $P1_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P1name'"))['ID'];
                        $P2_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `Name`= '$P2name'"))['ID'];
        
                        mysqli_query($conn, "INSERT INTO `duels` (`P1 ID`, `P2 ID`, `Last Played`) VALUES ('$P1_ID', '$P2_ID', '$LastPlayed')");

                        $Duel_ID = mysqli_fetch_assoc($conn->query("SELECT * FROM `duels` WHERE `P1 ID`= '$P1_ID' AND `P2 ID`= '$P2_ID'"))['ID'];
       
                        $_SESSION['first_time?'] = true;
                    }
                    
                    setcookie("Duel_ID", $Duel_ID, time() + 86400, '/');
                    
                    unset($_SESSION['function_called']);
                    
                    echo "<script> window.location.href = '/PHP/TicTacToe/Gamepage/gamepage.php' </script>";  
                }
            }
        }       
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="mainpage.css">

    <title>Tic Tac Toe by Rauf</title>

    <link rel="shortcut icon" href="../Icons/tictactoe-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> 
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 id="game-heading"><span><i class="fas fa-x"></i></span><span>TIC TAC TOE</span><span><i class="fas fa-o"></i></span></h1>
            <span id="git-link"> by <a href="https://github.com/Raid-dev" target="_blank"> Rauf </a></span>
        </div>

        <form method="POST" autocomplete="off" class="col-xs-10 col-s-11 col-m-9 col-l-7 col-xl-5">
            <div id="p1" class="players">
                <span> Player 1 </span> <br>
                <label for="p1-name">
                    <input type="text" name="p1-name" id="p1-name" class="names col-m-12 col-l-12" placeholder=" name" minlength="2" maxlength="20" pattern="[A-Za-z][A-Za-z0-9]*" title="Must start with a letter and can include numbers" required autofocus>
                </label> <br> <br>
                <label for="p1-password">
                    <input type="password" name="p1-password" id="p1-password" class="passwords col-m-12 col-l-12" placeholder=" password" minlength="8" maxlength="20" title="Please use at least 8 and at most 20 characters!" required>
                </label>
            </div>

            <div id="p2" class="players">
                <span> Player 2 </span> <br>
                <label for="p2-name">
                    <input type="text" name="p2-name" id="p2-name" class="names col-m-12 col-l-12" placeholder=" name" minlength="2" maxlength="20" pattern="[A-Za-z][A-Za-z0-9]*" title="Must start with a letter and can include numbers" required>
                </label> <br> <br>
                <label for="p2-password">
                    <input type="password" name="p2-password" id="p2-password" class="passwords col-m-12 col-l-12" placeholder=" password" minlength="8" maxlength="20" title="Please use at least 8 and at most 20 characters!" required>
                </label>
            </div>

            <button type="submit" id="play-btn" class="play-btn col-xs-4 col-s-4 col-m-4 col-l-4"><span class="text"> PLAY </span></button>
        </form>

        <div class="players-counter">Total Players : <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT ID FROM `players`")); ?></div>
    </div>

    <script src="mainpage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>