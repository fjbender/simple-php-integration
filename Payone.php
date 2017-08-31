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

require 'vendor/autoload.php';

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
     * @throws Exception
     * @return array
     */
    public static function sendRequest($request)
    {
        $client = new \GuzzleHttp\Client();

        echo "Requesting...";
        $begin = microtime(true);

        if ($response = $client->request('POST', self::PAYONE_SERVER_API_URL, ['form_params' => $request])) {
            $return = self::parseResponse($response);
        } else {
            throw new Exception('Something went wrong during the HTTP request.');
        }

        $end = microtime(true);
        $duration = $end - $begin;
        echo "done.\n";
        echo "Request took " . $duration . " seconds.\n";
        return $return;
    }

    /**
     * gets response string an puts it into an array
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @throws Exception
     * @return array
     */
    public static function parseResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        $responseArray = array();
        $explode = explode("\n", $response->getBody());
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
        if ($responseArray['status'] == "ERROR") {
            $msg = "Payone returned an error:\n" . print_r($responseArray, true);
            throw new Exception($msg);
        }
        return $responseArray;
    }
}