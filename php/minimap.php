<?php

    namespace minimap {
        function renderMiniMap(array $building_status) {
            foreach($building_status as $building_status_set) {
                foreach($building_status_set as $building) {
                    echo $building . "<br>";
                }
                echo "<hr>";
            }
        }

        function renderMiniMapFromUrl() {
            $match_details = getMatchDetails(getMatchId());
            $building_status = getBuildingStatusFromMatchDetails($match_details);

            renderMiniMap($building_status);
        }

        function renderMiniMapFromArray(array $building_status) {
            renderMiniMap($building_status);
        }

        function getBuildingStatusAsArray($tower_status_radiant, $barracks_status_radiant, $tower_status_dire, $barracks_status_dire) {
            $building_status_array = array();
            $building_status_array['tower_status_radiant'] = array_pad(convertStatusIntToArray($tower_status_radiant), 11, 0);
            $building_status_array['barracks_status_radiant'] = array_pad(convertStatusIntToArray($barracks_status_radiant), 6, 0);
            $building_status_array['tower_status_dire'] = array_pad(convertStatusIntToArray($tower_status_dire), 11, 0);
            $building_status_array['barracks_status_dire'] = array_pad(convertStatusIntToArray($barracks_status_dire), 6, 0);

            return $building_status_array;
        }

        function getMatchDetails($match_id) {
            // create a new cURL resource
            $request_match_details = curl_init();

            // set URL and other appropriate options
            curl_setopt($request_match_details, CURLOPT_URL, "http://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/v1/?match_id=" . $match_id . "&key=0BFD269FE91520E28E56CA39579AB6C6");
            curl_setopt($request_match_details, CURLOPT_HEADER, 0);
            curl_setopt($request_match_details, CURLOPT_RETURNTRANSFER, 1);

            // get match data from api object
            $match_details_json = curl_exec($request_match_details);
            $match_details_object = json_decode($match_details_json, true);

            // check response for problems
            if (isset($match_details_object['result']['error'])) {
                $error = $match_details_object['result']['error'];
                echo "Error recieved from dota2 api: " . $error;
                exit;
            }

            // close curl
            curl_close($request_match_details);

            return $match_details_object;
        }

        function getBuildingStatusFromMatchDetails($match_details) {
            // Radiant
            $tower_status_radiant = $match_details['result']['tower_status_radiant'];
            $barracks_status_radiant = $match_details['result']['barracks_status_radiant'];

            // Dire
            $tower_status_dire = $match_details['result']['tower_status_dire'];
            $barracks_status_dire = $match_details['result']['barracks_status_dire'];

            // convert to array of boolean values
            $building_status_array = getBuildingStatusAsArray($tower_status_radiant, $barracks_status_radiant, $tower_status_dire, $barracks_status_dire);

            return $building_status_array;
        }

        function convertStatusIntToArray($buildingStatus) {
            return str_split(decbin($buildingStatus));
        }

        function getMatchId() {
            $match_id = isset($_GET['match_id']) ? $_GET["match_id"] : null;

            if (!$match_id) {
                echo "No match_id argument provided in url";
                exit;
            } else {
                return $match_id;
            }
        }
    }
