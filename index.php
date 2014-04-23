<!DOCTYPE html>
<html>
    <head>
        <title>api doto best doto</title>
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
        <link rel="stylesheet" href="css/minimap.css">
    </head>
    <body>
        <?php
            require_once('php/minimap.php');
            minimap::renderMiniMapFromUrl();

            // for offline testing
            // $building_status = minimap::getBuildingStatusAsArray(1974, 0, 63, 0);
            // $building_status = minimap::getBuildingStatusAsArray(388, 51, 1975, 631);
            // minimap::renderMiniMapFromArray($building_status);

            // Further reading:
            // http://wiki.teamfortress.com/wiki/WebAPI/GetMatchDetails#Tower_Status
        ?>
    </body>
</html>
