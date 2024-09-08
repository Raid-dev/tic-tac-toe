<?php   
    include 'connection.php';

    if (isset($_COOKIE['Duel_ID']) && $_COOKIE['Duel_ID']) 
        $Duel_ID = $_COOKIE['Duel_ID'];
    
    $Duel = mysqli_fetch_assoc(($conn->query("SELECT * FROM `duels` WHERE `ID`= '$Duel_ID'")));
    
    $P1_ID = $Duel['P1 ID'];
    $P2_ID = $Duel['P2 ID'];

    $Duel_P1wins = $Duel['P1 wins'];
    $Duel_P2wins = $Duel['P2 wins'];
    $Duel_Draws = $Duel['Draws'];
    $Duel_Totalgames  = $Duel['Total games'];

    function LastDate() {
        date_default_timezone_set("Asia/Baku");

        $Date = date("Y-m-d H:i:s");

        return $Date;
    }

    $LastPLayed = LastDate();

    mysqli_query($conn, "UPDATE `duels` SET `Last Played` = '$LastPLayed' WHERE `ID` = '$Duel_ID'");
    mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$LastPLayed' WHERE `ID`= '$P1_ID'");
    mysqli_query($conn, "UPDATE `players` SET `Last Played` = '$LastPLayed' WHERE `ID`= '$P2_ID'");

    $P1 = mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `ID`= '$P1_ID'")));
    
    $P1wins = $P1['Wins'];
    $P1loses = $P1['Loses'];
    $P1draws = $P1['Draws'];
    $P1totalgames  = $P1['Total games'];
    
    $P2 = mysqli_fetch_assoc(($conn->query("SELECT * FROM `players` WHERE `ID`= '$P2_ID'")));

    $P2wins = $P2['Wins'];
    $P2loses = $P2['Loses'];
    $P2draws = $P2['Draws'];
    $P2totalgames = $P2['Total games'];

    if (isset($_POST['item']) && $_POST['item']) {
        $item = $_POST['item'];
        
        if ($item == "P1 Won") {
            mysqli_query($conn, "UPDATE `duels` SET `P1 wins` = $Duel_P1wins + 1 WHERE `ID`= '$Duel_ID'");
            mysqli_query($conn, "UPDATE `players` SET `Wins` = $P1wins + 1 WHERE `ID`= '$P1_ID'");
            mysqli_query($conn, "UPDATE `players` SET `Loses` = $P2loses + 1 WHERE `ID`= '$P2_ID'");
        }
        else if ($item == "P2 Won") {
            mysqli_query($conn, "UPDATE `duels` SET `P2 wins` = $Duel_P2wins + 1 WHERE `ID`= '$Duel_ID'");
            mysqli_query($conn, "UPDATE `players` SET `Wins` = $P2wins + 1 WHERE `ID`= '$P2_ID'");
            mysqli_query($conn, "UPDATE `players` SET `Loses` = $P1loses + 1 WHERE `ID`= '$P1_ID'");
        }
        else if ($item == "Draw") {
            mysqli_query($conn, "UPDATE `duels` SET `Draws` = $Duel_Draws + 1 WHERE `ID`= '$Duel_ID'");
            mysqli_query($conn, "UPDATE `players` SET `Draws` = $P1draws + 1 WHERE `ID`= '$P1_ID'");
            mysqli_query($conn, "UPDATE `players` SET `Draws` = $P2draws + 1 WHERE `ID`= '$P2_ID'");
        }    

        mysqli_query($conn, "UPDATE `duels` SET `Total games`= $Duel_Totalgames + 1 WHERE `ID`= '$Duel_ID'");
        mysqli_query($conn, "UPDATE `players` SET `Total games`= $P1totalgames + 1 WHERE `ID`= '$P1_ID'");
        mysqli_query($conn, "UPDATE `players` SET `Total games`= $P2totalgames + 1 WHERE `ID`= '$P2_ID'");
    }
    
    header("Location: /PHP/TicTacToe/AdminPanel/AccessDenied/accessDenied.html");
?>