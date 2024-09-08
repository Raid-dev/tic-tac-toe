<?php
    include 'connection.php';
    
    session_start();

    if(isset($_COOKIE['Duel_ID']) && $_COOKIE['Duel_ID'])
        $Duel_ID = $_COOKIE['Duel_ID'];
    else
        header("Location: ../AccessDenied/accessDenied.html");

    $Duel = mysqli_fetch_assoc($conn->query("SELECT * FROM `duels` WHERE `ID`= '$Duel_ID'"));

    $P1_ID = $Duel['P1 ID'];
    $P2_ID = $Duel['P2 ID'];

    $P1 = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `ID`= '$P1_ID'"));
    $P2 = mysqli_fetch_assoc($conn->query("SELECT * FROM `players` WHERE `ID`= '$P2_ID'"));
    
    $P1name = $P1['Name'];
    $P2name = $P2['Name'];

    if (!isset($_SESSION['function_called'])) {
        if (isset($_SESSION['first_time?']))
            echo $_SESSION['first_time?'] ? "<script> alert('Welcome dear $P1name and $P2name !') </script>" : "<script> alert('Welcome Back dear $P1name and $P2name !') </script>";
        else
            header("Location: ../AccessDenied/accessDenied.html");
        $_SESSION['function_called'] = true;
    }

    $Duel_P1wins = $Duel['P1 wins'];
    $Duel_P2wins = $Duel['P2 wins'];

    $P1wins = $P1['Wins'];
    $P2wins = $P2['Wins'];

    $P1totalgames = $P1['Total games'];
    $P2totalgames = $P2['Total games'];

    $RankTable = "SELECT `ID`, DENSE_RANK() OVER(ORDER BY `Wins` DESC) AS RANK FROM `players` ORDER BY RANK";

    $P1rank = mysqli_fetch_assoc($conn->query("SELECT `RANK` FROM ($RankTable) AS RankTable WHERE `ID` = '$P1_ID'"))['RANK'];
    $P2rank = mysqli_fetch_assoc($conn->query("SELECT `RANK` FROM ($RankTable) AS RankTable WHERE `ID` = '$P2_ID'"))['RANK'];

    function LastDate() { 
        date_default_timezone_set("Asia/Baku");

        $Date = date("Y-m-d H:i:s");

        return $Date;
    }

    $Date = LastDate();

    mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `feedbacks` WHERE `Player ID`='$P1_ID'")) == 0 ? $P1permission = "allowed" : $P1permission = "disallowed";
    mysqli_num_rows(mysqli_query($conn, "SELECT * FROM `feedbacks` WHERE `Player ID`='$P2_ID'")) == 0 ? $P2permission = "allowed" : $P2permission = "disallowed";

    if (isset($_POST['feedback1']) && $_POST['feedback1'] && $P1permission = "allowed") {
        $P1feedback = ucfirst($_POST['feedback1']);
        mysqli_query($conn, "INSERT INTO `feedbacks` (`Player ID`, `Feedback`, `Date`) VALUES ('$P1_ID', '$P1feedback', '$Date')");
        $P1permission = "disallowed";
    }        

    if (isset($_POST['feedback2']) && $_POST['feedback2'] && $P2permission = "allowed") {
        $P2feedback = ucfirst($_POST['feedback2']);
        mysqli_query($conn, "INSERT INTO `feedbacks` (`Player ID`, `Feedback`, `Date`) VALUES ('$P2_ID', '$P2feedback', '$Date')");
        $P2permission = "disallowed";
    }
?>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="gamepage.css">
    
    <title> Tic Tac Toe by Rauf </title>

    <link rel="shortcut icon" href="../Icons/tictactoe-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
</head>
<script type="text/javascript"> 
        var P1name = "<?= $P1name ?>";
        var P2name = "<?= $P2name ?>"; 
        var P1totalgames = "<?= $P1totalgames ?>";
        var P2totalgames = "<?= $P2totalgames ?>"; 
        var P1permission = "<?= $P1permission ?>";
        var P2permission = "<?= $P2permission ?>";
</script>
<body>
    <div class="feedback-window">
        <form method="post" autocapitalize="sentences" autocomplete="off">
            <h2 style="text-align: center;"> We'll appreciate your feedbacks ! </h2> <br>

            <div class="player-feedbacks" id="p1-feedback" style="float: left">
                <label for="feedback1"> | <?php echo $P1name ?> | </label> <br>
                <textarea id="feedback1" name="feedback1" cols="34" rows="6" maxlength="400" placeholder=" maximum 400 characters" pattern="[A-Za-z].*" title="Must start with a letter" required></textarea>
            </div>

            <div class="player-feedbacks" id="p2-feedback" style="float: right">
                <label for="feedback2"> | <?php echo $P2name ?> | </label> <br>
                <textarea id="feedback2" name="feedback2" cols="34" rows="6" maxlength="400" placeholder=" maximum 400 characters" pattern="[A-Za-z].*" title="Must start with a letter" required></textarea>
            </div> <br>

            <button type="submit" class="send-feedback-btn"> Send </button>
        </form>
    </div>

    <div class="sides-container">
        <div class="sides p1-side">
            <span class="names p1-name"><span> X </span> <?php echo $P1name; ?></span> <br> <br>
            <span class="duel-wins duel-p1-wins"><?php echo $Duel_P1wins; ?></span> <br> <br>
            <span>Total: </span><span class="player-wins player-p1-wins"><?php echo $P1wins; ?></span> <br>
            <span>Rank: #</span><span class="ranks p1-rank"><?php echo $P1rank; ?></span>
        </div>  
        <div class="sides p2-side">
            <span class="names p2-name"> <?php echo $P2name; ?> <span> O</span></span> <br> <br>
            <span class="duel-wins duel-p2-wins"><?php echo $Duel_P2wins; ?></span> <br> <br>
            <span>Total: </span><span class="player-wins player-p2-wins"><?php echo $P2wins; ?></span> <br>
            <span>Rank: #</span><span class="ranks p2-rank"><?php echo $P2rank; ?></span>
        </div>
    </div>

    <span class="prompt"></span>

    <span class="line line"></span>
    
    <div class="container">
        <div class="plate-container">
            <span class="plates plate-1">
                <span class="sign x-1">X</span>
                <span class="sign o-1">O</span>
            </span>
    
            <span class="plates plate-2">
                <span class="sign x-2">X</span>
                <span class="sign o-2">O</span>
            </span>
    
            <span class="plates plate-3">
                <span class="sign x-3">X</span>
                <span class="sign o-3">O</span>
            </span>
            
            <span class="plates plate-4">
                <span class="sign x-4">X</span>
                <span class="sign o-4">O</span>
            </span>
    
            <span class="plates plate-5">
                <span class="sign x-5">X</span>
                <span class="sign o-5">O</span>
            </span>
    
            <span class="plates plate-6">
                <span class="sign x-6">X</span>
                <span class="sign o-6">O</span>
            </span>
    
            <span class="plates plate-7">
                <span class="sign x-7">X</span>
                <span class="sign o-7">O</span>
            </span>
    
            <span class="plates plate-8">
                <span class="sign x-8">X</span>
                <span class="sign o-8">O</span>
            </span>
    
            <span class="plates plate-9">
                <span class="sign x-9">X</span>
                <span class="sign o-9">O</span>
            </span>
        </div>
    </div>

    <a href="/PHP/TicTacToe/Mainpage/mainpage.php">
        <button type="button" class="quit-btn" role="button"><span class="text">QUIT</span></button>
    </a>

    <label for="rotation-btn">
        <input type="checkbox" name="rotation-btn" id="rotation-btn" class="rotation-btn">Rotation
    </label>

    <script src="gamepage.js"></script>  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>