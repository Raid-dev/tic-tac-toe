function CheckInputs() {
    const name1 = document.getElementById('p1-name').value;
    const name2 = document.getElementById('p2-name').value;

    if (!name1[name1.length-1])
        document.querySelector('p1-name').value = substr(name1, 0, name1.length-1);
    if (!name2[name2.length-1])
        document.querySelector('p2-name').value = substr(name2, 0, name2.length-1);


    if ((name1 && name2) && (name1.toLowerCase() === name2.toLowerCase())) {
        alert("Nicknames cannot be the same !");
        document.getElementById('p2-name').value = "";
        document.getElementById('p2-name').focus();
    }
}

document.getElementById('play-btn').addEventListener("mouseover", CheckInputs)
document.getElementById('play-btn').addEventListener("click", CheckInputs)

if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}