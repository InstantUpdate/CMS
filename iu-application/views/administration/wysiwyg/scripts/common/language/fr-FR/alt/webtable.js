function loadTxt() {
    document.getElementById("tab0").innerHTML = "INSÉRER";
    document.getElementById("tab1").innerHTML = "MODIFIER";
    document.getElementById("tab2").innerHTML = "FORMAT AUTO";
    document.getElementById("btnDelTable").value = "Efface le tableau sélectionné";
    document.getElementById("btnIRow1").value = "Insère ligne (Sous)";
    document.getElementById("btnIRow2").value = "Insère ligne (Sur)";
    document.getElementById("btnICol1").value = "Insère Colonne (Gauche)";
    document.getElementById("btnICol2").value = "Insère Colonne (Droite)";
    document.getElementById("btnDelRow").value = "Effacer ligne";
    document.getElementById("btnDelCol").value = "Effacer colonne";
    document.getElementById("btnMerge").value = "Fusionne cellules";
    document.getElementById("lblFormat").innerHTML = "FORMAT:";
    document.getElementById("lblTable").innerHTML = "Tableau";
    document.getElementById("lblEven").innerHTML = "Lignes paires";
    document.getElementById("lblOdd").innerHTML = "Lignes impaires";
    document.getElementById("lblCurrRow").innerHTML = "Ligne courante";
    document.getElementById("lblCurrCol").innerHTML = "Colonne courante";
    document.getElementById("lblBg").innerHTML = "FOND:";
    document.getElementById("lblText").innerHTML = "TEXT:";
    document.getElementById("lblBorder").innerHTML = "BORDURE:";
    document.getElementById("lblThickness").innerHTML = "Épaisseur:";
    document.getElementById("lblColor").innerHTML = "Couleur:";
    document.getElementById("lblCellPadding").innerHTML = "MARGE INTÉRIEURE:";
    document.getElementById("lblFullWidth").innerHTML = "Full Width";
    document.getElementById("lblAutofit").innerHTML = "Autofit";
    document.getElementById("lblFixedWidth").innerHTML = "Fixed Width:";
    document.getElementById("lnkClean").innerHTML = "CLEAN";
    document.getElementById("lblTextAlign").innerHTML = "TEXT ALIGN:";
    document.getElementById("btnAlignLeft").value = "Left";
    document.getElementById("btnAlignCenter").value = "Center";
    document.getElementById("btnAlignRight").value = "Right";
    document.getElementById("btnAlignTop").value = "Top";
    document.getElementById("btnAlignMiddle").value = "Middle";
    document.getElementById("btnAlignBottom").value = "Bottom";  
}
function writeTitle() {
    document.write("<title>" + "Tableau" + "</title>")
}
function getTxt(s) {
    switch (s) {
        case "Clean Formatting": return "Clean Formatting";
    }
}