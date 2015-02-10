<?php

require_once(dirname(dirname(__FILE__)) . '/externals/Requests/library/Requests.php');
Requests::register_autoloader();


class CheckmywsClient {
    const VERSION = '0.1';
    const BASE_URL = 'https://api.checkmy.ws/api';

    public function request($path, $method="GET", $params=NULL, $data=NULL, $status_code=200) {
        $url = self::BASE_URL . $path;

        $headers = array();
        $options = array();

        $response = Requests::request($url, $headers, $data, $method, $options);

        if ($response->status_code != $status_code)
            return NULL;

        if ($response->headers["content-type"] == "application/json")
            return json_decode($response->body);

        return $response->body;
    }

    public function status($check_id) {
        $path = "/status/" . $check_id;

        return $this->request($path, "GET");
    }

    public function logs($check_id) {
        $path = "/status/logs/" . $check_id;

        return $this->request($path, "GET");
    }

    public function metrics($check_id, $timewindow=NULL) {
        if ($timewindow)
            $path = "/status/metrics/" . $timewindow . "/" . $check_id;
        else
            $path = "/status/metrics/" . $check_id;

        return $this->request($path, "GET");
    }
}

?>