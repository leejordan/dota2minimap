<?php

    namespace minimap {
        const API_KEY = "<your api key>";

        function renderMiniMap(array $building_status) {
            echo "<div class='minimap_wrap'>";
                echo "<img class='minimap' src='images/minimap.png'/>";

                // radiant towers
                $tower_status_radiant = $building_status['tower_status_radiant'];
                foreach($tower_status_radiant as $key => $value) {
                    if($value == 1) {
                        echo "<div class='building tower $key'></div>";
                    }
                }

                // radiant rax
                $rax_status_radiant = $building_status['rax_status_radiant'];
                foreach($rax_status_radiant as $key => $value) {
                    if($value == 1) {
                        echo "<div class='building rax $key'></div>";
                    }
                }

                // dire towers
                $tower_status_dire = $building_status['tower_status_dire'];
                foreach($tower_status_dire as $key => $value) {
                    if($value == 1) {
                        echo "<div class='building tower dire $key'></div>";
                    }
                }

                // dire rax
                $rax_status_dire = $building_status['rax_status_dire'];
                foreach($rax_status_dire as $key => $value) {
                    if($value == 1) {
                        echo "<div class='building rax dire $key'></div>";
                    }
                }



            echo "</div>";
        }

        function renderMiniMapFromUrlOrPost() {
            $match_details = getMatchDetails(getMatchId());
            $building_status = getBuildingStatusFromMatchDetails($match_details);

            renderMiniMap($building_status);
        }

        function renderMiniMapFromArray(array $building_status) {
            renderMiniMap($building_status);
        }

        function getBuildingStatusAsArray($tower_status_radiant, $rax_status_radiant, $tower_status_dire, $rax_status_dire) {
            // set up an array of keys
            $tower_status_radiant_array_keys = array('rad_top_t4', 'rad_bot_t4', 'rad_bot_t3', 'rad_bot_t2', 'rad_bot_t1', 'rad_mid_t3', 'rad_mid_t2', 'rad_mid_t1', 'rad_top_t3', 'rad_top_t2', 'rad_top_t1');
            $rax_status_radiant_array_keys = array('rad_rax_bot_ranged', 'rad_rax_bot_melee', 'rad_rax_mid_ranged', 'rad_rax_mid_melee', 'rad_rax_top_ranged', 'rad_rax_top_melee');
            $tower_status_dire_array_keys = array('dire_top_t4', 'dire_bot_t4', 'dire_bot_t3', 'dire_bot_t2', 'dire_bot_t1', 'dire_mid_t3', 'dire_mid_t2', 'dire_mid_t1', 'dire_top_t3', 'dire_top_t2', 'dire_top_t1');
            $rax_status_dire_array_keys = array('dire_rax_bot_ranged', 'dire_rax_bot_melee', 'dire_rax_mid_ranged', 'dire_rax_mid_melee', 'dire_rax_top_ranged', 'dire_rax_top_melee');

            // trim our array to remove the unused bits
            $tower_status_radiant_array_values = array_pad(convertStatusIntToArray($tower_status_radiant), -11, 0);
            $rax_status_radiant_array_values = array_pad(convertStatusIntToArray($rax_status_radiant), -6, 0);
            $tower_status_dire_array_values = array_pad(convertStatusIntToArray($tower_status_dire), -11, 0);
            $rax_status_dire_array_values = array_pad(convertStatusIntToArray($rax_status_dire), -6, 0);

            // combine our keys and values together
            $tower_status_radiant_array_keyed = array_combine($tower_status_radiant_array_keys, $tower_status_radiant_array_values);
            $rax_status_radiant_array_keyed = array_combine($rax_status_radiant_array_keys, $rax_status_radiant_array_values);
            $tower_status_dire_array_keyed = array_combine($tower_status_dire_array_keys, $tower_status_dire_array_values);
            $rax_status_dire_array_keyed = array_combine($rax_status_dire_array_keys, $rax_status_dire_array_values);

            // populate our final formatted array
            $building_status_array = array();
            $building_status_array['tower_status_radiant'] = $tower_status_radiant_array_keyed;
            $building_status_array['rax_status_radiant'] = $rax_status_radiant_array_keyed;
            $building_status_array['tower_status_dire'] = $tower_status_dire_array_keyed;
            $building_status_array['rax_status_dire'] = $rax_status_dire_array_keyed;

            return $building_status_array;
        }

        function getMatchDetails($match_id) {
            // create a new cURL resource
            $request_match_details = curl_init();

            // set URL and other appropriate options
            curl_setopt($request_match_details, CURLOPT_URL, "http://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/v1/?match_id=" . $match_id . "&key=" . API_KEY);
            curl_setopt($request_match_details, CURLOPT_HEADER, 0);
            curl_setopt($request_match_details, CURLOPT_RETURNTRANSFER, 1);

            // get match data from api object
            $match_details_json = curl_exec($request_match_details);
            $match_details_object = json_decode($match_details_json, true);

            // check response is not empty
            if (empty($match_details_json)) {
                echo "API response is empty. Could the steam api be down?";
                exit;
            }

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
            $rax_status_radiant = $match_details['result']['barracks_status_radiant'];

            // Dire
            $tower_status_dire = $match_details['result']['tower_status_dire'];
            $rax_status_dire = $match_details['result']['barracks_status_dire'];

            // debug
            // echo "$tower_status_radiant, $rax_status_radiant, $tower_status_dire, $rax_status_dire";

            // convert to final array format
            $building_status_array = getBuildingStatusAsArray($tower_status_radiant, $rax_status_radiant, $tower_status_dire, $rax_status_dire);

            return $building_status_array;
        }

        function convertStatusIntToArray($buildingStatus) {
            return str_split(decbin($buildingStatus));
        }

        function getMatchId() {
            $match_id = isset($_GET['match_id']) ? $_GET["match_id"] : null;
            if (!$match_id) {
                $match_id = isset($_POST['match_id']) ? $_POST["match_id"] : null;
            }

            if (!$match_id) {
                echo "No match_id argument provided in url";
                exit;
            } else {
                return $match_id;
            }
        }
    }
