const plates = document.querySelectorAll(".plates");
const promptt = document.querySelector(".prompt");
const line = document.querySelector(".line");
const quit_btn = document.querySelector(".quit-btn");
const rotation_btn = document.querySelector(".rotation-btn");

var p1_rank = parseInt(document.querySelector(".p1-rank").innerHTML.slice(1));
var p2_rank = parseInt(document.querySelector(".p2-rank").innerHTML.slice(1));

var p1_rank = "";
var p2_rank = "";

var whose_turn = "";
var plate_id = "";
var selected_plates = [];
var count = 0;
var game_status = "";
var feedbacks_taken = "";

(P1permission == "allowed" || P2permission == "allowed") ? feedbacks_taken = false : feedbacks_taken = true;

rotation_btn.onchange = () => {
    if(rotation_btn.checked) {
        if(whose_turn == "p1")
            document.body.style.transform = 'rotate(0deg)';
        else if(whose_turn == "p2")
            document.body.style.transform = 'rotate(180deg)';
    }
    else {
        document.body.style.transform = 'rotate(0deg)';
    }
}

start_game();

feedbacks_taken == false ? setTimeout(check4feedback(), 2000) : "";

function start_game() {
    game_status = "new game";

    var random_number = Math.floor(2*Math.random() + 1);

    switch (random_number) {
        case 1:
            if(rotation_btn.checked) {
                document.body.style.transform = 'rotate(0deg)';
            }
            whose_turn = "p1";
            promptt.innerHTML = P1name + " starts";
            break;
        case 2:
            if(rotation_btn.checked) {
                document.body.style.transform = 'rotate(180deg)';
            }
            whose_turn = "p2";
            promptt.innerHTML = P2name + " starts";
            break;
    }
}

plates.forEach(plate => {
    plate.onclick = function() {
        plate_id = plate.className[plate.className.length - 1];

        if(!selected_plates[plate_id - 1] && game_status == "new game") {
            if (whose_turn == "p1") {
                selected_plates[plate_id - 1] = "x";
                document.querySelector(".x-" + plate_id).style.display = "inline-block";
                if(rotation_btn.checked) {
                    document.body.style.transform = 'rotate(180deg)';
                }
                whose_turn = "p2";
                promptt.innerHTML = P2name + "'s turn";
            }
            else if (whose_turn == "p2") {
                selected_plates[plate_id - 1] = "o";
                document.querySelector(".o-" + plate_id).style.display = "inline-block";
                if(rotation_btn.checked) {
                    document.body.style.transform = 'rotate(0deg)';
                }
                whose_turn = "p1";
                promptt.innerHTML = P1name + "'s turn";
            }
            
            check_status(selected_plates);
        }
    }
})

function check_status(arr) {
    if((arr[0] == "x" && arr[1] == "x" && arr[2] == "x") || (arr[0] == "o" && arr[1] == "o" && arr[2] == "o"))
        line.className += "-1";
    else if((arr[3] == "x" && arr[4] == "x" && arr[5] == "x") || (arr[3] == "o" && arr[4] == "o" && arr[5] == "o"))
        line.className += "-2";
    else if((arr[6] == "x" && arr[7] == "x" && arr[8] == "x") || (arr[6] == "o" && arr[7] == "o" && arr[8] == "o"))
        line.className += "-3";
    else if((arr[0] == "x" && arr[3] == "x" && arr[6] == "x") || (arr[0] == "o" && arr[3] == "o" && arr[6] == "o"))
        line.className += "-4";
    else if((arr[1] == "x" && arr[4] == "x" && arr[7] == "x") || (arr[1] == "o" && arr[4] == "o" && arr[7] == "o"))
        line.className += "-5";
    else if((arr[2] == "x" && arr[5] == "x" && arr[8] == "x") || (arr[2] == "o" && arr[5] == "o" && arr[8] == "o"))
        line.className += "-6";
    else if((arr[0] == "x" && arr[4] == "x" && arr[8] == "x") || (arr[0] == "o" && arr[4] == "o" && arr[8] == "o"))
        line.className += "-7";
    else if((arr[2] == "x" && arr[4] == "x" && arr[6] == "x") || (arr[2] == "o" && arr[4] == "o" && arr[6] == "o"))
        line.className += "-8";

    if ((arr[0] == "x" && arr[1] == "x" && arr[2] == "x") || (arr[3] == "x" && arr[4] == "x" && arr[5] == "x") || (arr[6] == "x" && arr[7] == "x" && arr[8] == "x") || (arr[0] == "x" && arr[3] == "x" && arr[6] == "x") || (arr[1] == "x" && arr[4] == "x" && arr[7] == "x") || (arr[2] == "x" && arr[5] == "x" && arr[8] == "x") || (arr[0] == "x" && arr[4] == "x" && arr[8] == "x") || (arr[2] == "x" && arr[4] == "x" && arr[6] == "x")) {
        game_status = "ended";
        promptt.innerHTML = P1name + " WON !";
        line.style.display = "inline-block";
        document.querySelector(".duel-p1-wins").innerHTML = parseInt(document.querySelector(".duel-p1-wins").innerHTML) + 1;
        document.querySelector(".player-p1-wins").innerHTML = parseInt(document.querySelector(".player-p1-wins").innerHTML) + 1;
        $(document).ready(function(){
            $.ajax({
                url:"update_data.php",
                method:'POST',
                data: {
                    item: "P1 Won",
                },
            });
        });
        
        P1totalgames = parseInt(P1totalgames) + 1;
        P2totalgames = parseInt(P2totalgames) + 1;

        feedbacks_taken == false ? setTimeout(check4feedback(), 2000) : "";

        setTimeout(reset_game, 3000);
    }
    else if((arr[0] == "o" && arr[1] == "o" && arr[2] == "o") || (arr[3] == "o" && arr[4] == "o" && arr[5] == "o") || (arr[6] == "o" && arr[7] == "o" && arr[8] == "o") || (arr[0] == "o" && arr[3] == "o" && arr[6] == "o") || (arr[1] == "o" && arr[4] == "o" && arr[7] == "o") || (arr[2] == "o" && arr[5] == "o" && arr[8] == "o") || (arr[0] == "o" && arr[4] == "o" && arr[8] == "o") || (arr[2] == "o" && arr[4] == "o" && arr[6] == "o")) {
        game_status = "ended";
        promptt.innerHTML = P2name + " WON !";
        line.style.display = "inline-block";
        document.querySelector(".duel-p2-wins").innerHTML = parseInt(document.querySelector(".duel-p2-wins").innerHTML) + 1;
        document.querySelector(".player-p2-wins").innerHTML = parseInt(document.querySelector(".player-p2-wins").innerHTML) + 1;
        $(document).ready(function(){
            $.ajax({
                url:"update_data.php",
                method:'POST',
                data: {
                    item: "P2 Won",
                },
            });
        });

        P1totalgames = parseInt(P1totalgames) + 1;
        P2totalgames = parseInt(P2totalgames) + 1;

        feedbacks_taken == false ? setTimeout(check4feedback(), 2000) : "";

        setTimeout(reset_game, 3000);
    }
    else {
        count = 0;
        for (let i = 0; i < 9; i++) {
            (!arr[i]) ? count++ : "";    
        }
        if (count == 0) {
            game_status = "ended";
            promptt.innerHTML = "DRAW";
            $(document).ready(function(){
                $.ajax({
                    url:"update_data.php",
                    method:'POST',
                    data: {
                        item: "Draw",
                    },
                });
            });

            feedbacks_taken == false ? setTimeout(check4feedback(), 2000) : "";

            setTimeout(reset_game, 3000);
        }
    }
}

function check4feedback() {
    if (Math.min(P1totalgames, P2totalgames) == 2) {
        const feedback_window = document.querySelector('.feedback-window');
    
        const p1_feedback = document.getElementById('p1-feedback');
        const p2_feedback = document.getElementById('p2-feedback');

        const send_btn = document.querySelector('.send-feedback-btn');

        feedback_window.style.display = "inline-block";
        
        if (P1permission == "allowed" && P2permission == "allowed") {
            p1_feedback.style.display = "inline-block";
            p2_feedback.style.display = "inline-block"; 
        }
        else {
            if (P1permission == "allowed") {
                p1_feedback.style.display = "inline-block";

                p2_feedback.innerHTML = P2name + "'s feedback has been sent !";
                p2_feedback.style.display = "inline-block"
            }
            else {
                p2_feedback.style.display = "inline-block";
            
                p1_feedback.innerHTML = P1name + "'s feedback has been sent !";
                p1_feedback.style.display = "inline-block"; 
            }
        }

        send_btn.onclick = () => {
            if (P1permission == "disallowed" && P2permission == "disallowed") {
                feedback_window.style.display = "none";
            }
        }

        feedbacks_taken = true;
    }
} 

function reset_game() {
    for (let i = 0; i < 9; i++) {
        if(selected_plates[i] == "x") {
            document.querySelector(".x-" + (i+1)).style.display = "none";
            selected_plates[i] = "";
        }
        else if(selected_plates[i] == "o") {
            document.querySelector(".o-" + (i+1)).style.display = "none";
            selected_plates[i] = "";
        }
    }               
    
    line.className = "line line";
    line.style.display = "none";

    game_status = "new game";
    
    start_game();
}

function deleteCookie(cookieName) {
    document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
}

quit_btn.addEventListener('click', () => {
    deleteCookie("Duel_ID");
});


if (window.history.replaceState) {
    window.history.replaceState( null, null, window.location.href );
}