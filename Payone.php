<?php
/**
 * This class is a wrapper to be able to send arrays of Payone request
 * to the Payone platform.
 *
 * Payone Connector is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Payone Connector is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Payone Connector. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Simple PHP Integration
 * @link http://www.payone.de
 * @copyright (C) PAYONE GmbH 2016
 * @author Florian Bender <florian.bender@payone.de>
 * @author Timo Kuchel <timo.kuchel@payone.de>
 */
/**
 * Class Payone
 */
class Payone {

    /**
     * The URL of the Payone API
     */
    const PAYONE_SERVER_API_URL = 'https://api.pay1.de/post-gateway/';

    /**
     * performing the curl POST request to the PAYONE platform
     *
     * @param array $request
     * @return array
     */
    public static function doCurl($request)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::PAYONE_SERVER_API_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        curl_setopt($curl, CURLOPT_HEADER, false);
        echo "Requesting...";
        $begin = microtime(true);
        if ($response = curl_exec($curl)) {
            $return = self::parseResponse($response);
        }
        curl_close($curl);
        $end = microtime(true);
        $duration = $end - $begin;
        echo "done.\n";
        echo "Request took " . $duration . " seconds.\n";
        return $return;
    }

    /**
     * gets response string an puts it into an array
     *
     * @param string $response
     * @return array
     */
    public static function parseResponse($response)
    {
        $responseArray = array();
        $explode = explode(PHP_EOL, $response);
        foreach ($explode as $e) {
            $keyValue = explode("=", $e);
            if (trim($keyValue[0]) != "") {
                if (count($keyValue) == 2) {
                    $responseArray[$keyValue[0]] = trim($keyValue[1]);
                } else {
                    $key = $keyValue[0];
                    unset($keyValue[0]);
                    $value = implode("=", $keyValue);
                    $responseArray[$key] = $value;
                }
            }
        }
        return $responseArray;
    }
}