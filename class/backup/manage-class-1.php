<?php
include "../../dbconnect.php";
OpenSession();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - Class Connect List</title>
    <link rel="stylesheet" href="../../css/navbar.css">
    <link rel="stylesheet" href="../../css/sections.css">
    <link rel="stylesheet" href="../../css/backup/manage-class-subject.css">
</head>

<body>
    <?php DisplayNavHeader(); ?>

    <!-- SAMPLE SUBJECT -->

    <div class="btn-container">
        <div class="buttons">
            <button type="button" class="button">ADD SUBJECT</button>

        </div>
    </div>
    </div>

    <div id="container" class="container">
        <div id="subj-frame" class="subj-frame">
            <div class="subj-name-and-details">
                <p class="subj_name">
                    CAPSTONE PROJECT 2
                </p>
                <div class="subj_details semi-bold">GEED 10013</div>
            </div>
            <div class="professor valign-text-middle">SEVILLA, MARIA ANGELICA</div>

            <div class="subj-sched valign-text-middle">
                <div class="day semi-bold">WED</div>
                <div class="time">1:30PM - 3:00PM</div>
            </div>
            <div class="subj-sched">
                <div class="day semi-bold">SAT</div>
                <div class="time">1:30PM - 3:00PM</div>
            </div>
        </div>
    </div>

    <script>
        /** FOR DISPLAY PURPOSES ONLY; DAPAT GALING SA DB YUNG */
        function repeatDiv(div, num, parent) {
            for (var i = 0; i < num; i++) {
                // create a new div element
                var newDiv = document.createElement("div");
                // copy the content and style of the original div
                newDiv.innerHTML = div.innerHTML;
                newDiv.style.cssText = div.style.cssText;
                // copy the class of the original div
                newDiv.className = div.className;
                // add the new div to the parent div
                parent.appendChild(newDiv);
            }
        }

        var originalDiv = document.getElementById("subj-frame");
        var parentDiv = document.getElementById("container");
        repeatDiv(originalDiv, 3, parentDiv);
    </script>
</body>

</html>