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
            // minimap\renderMiniMapFromUrl();

            // all towers
            $building_status = minimap\getBuildingStatusAsArray(2047, 63, 2047, 63);

            // random
            // $building_status = minimap\getBuildingStatusAsArray(1974, 0, 63, 0);
            minimap\renderMiniMapFromArray($building_status);
        ?>
    </body>
</html>
