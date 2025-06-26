<?php
// index.php - main page
?>
<!DOCTYPE html>
<html lang="en">            
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>UniWebPlay</title>
        <link rel="stylesheet" href="style.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    </head>
    <body>
            <div id="container">
                <h1>Welcome to UniWebPlay</h1>
                <p>This is a university recreational facility management software.</p>
                <button id="startButton">Start</button>

                <h2>Book a Facility Slot</h2>
                <form id="bookingForm">
                    <label for="user_id">User ID:</label>
                    <input type="number" id="user_id" name="user_id" required />
                    <br />
                    <label for="facility_id">Facility ID:</label>
                    <input type="number" id="facility_id" name="facility_id" required />
                    <br />
                    <label for="slot">Slot (YYYY-MM-DD HH:MM:SS):</label>
                    <input type="text" id="slot" name="slot" required />
                    <br />
                    <button type="submit">Book Slot</button>
                </form>

                <h2>Report Maintenance Issue</h2>
                <form id="reportForm">
                    <label for="facility_id_report">Facility ID:</label>
                    <input type="number" id="facility_id_report" name="facility_id_report" required />
                    <br />
                    <label for="photo_url">Photo URL:</label>
                    <input type="text" id="photo_url" name="photo_url" />
                    <br />
                    <button type="submit">Report Issue</button>
                </form>

                <div id="responseMessage"></div>
            </div>
        <script src="script.js"></script>
        </body>
</html>
