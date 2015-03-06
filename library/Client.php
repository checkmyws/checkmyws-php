<?php

include(dirname(dirname(__FILE__)) . '/externals/httpful.phar');
use \Httpful\Request;

class CheckmywsClient {
    const VERSION = "0.1";

    public function __construct($login=NULL, $passwd=NULL) {
        $this->base_url = "https://api.checkmy.ws/api";

        $this->strict_ssl = True;
        $this->session = NULL;
        $this->authed = false;
        $this->account = NULL;
        $this->timeout = 5;
        $this->cookie = NULL;
        $this->useragent = 'checkmyws-php-' . self::VERSION;

        $this->login = $login;
        $this->passwd = $passwd;

        if ($passwd && strlen($passwd) != 40)
            $this->passwd = sha1($passwd);
    }

    public function request($path, $method="GET", $data=NULL, $status_code=200, $timeout=NULL) {
        $url = $this->base_url . $path;

        if ($method == "POST")
            $request = Request::post($url);

        else if ($method == "DELETE")
            $request = Request::delete($url);

        else
            $request = Request::get($url);

        $headers = array(
            "useragent" => $this->useragent
        );

        if ($this->cookie)
            $headers["Cookie"] = $this->cookie;

        if (! $timeout)
            $timeout = $this->timeout;

        $request->addHeaders($headers)
                ->strictSSL($this->strict_ssl)
                ->timeout($timeout);

        if ($data){
            $data = json_encode($data);
            $request = $request->sendsJson()
                               ->body($data);
        }
        
        try {
            $response = $request->send();

        } catch (Exception $e) {
            return NULL;
        }

        if ($response->headers['set-cookie'])
            $this->cookie = split(";", $response->headers['set-cookie'])[0];

        if ($response->code != $status_code)
            return NULL;

        return $response->body;
    }

    public function signin() {
        if ($this->login == NULL || $this->passwd == NULL)
            return NULL;

        if ($this->account)
            return;

        $path = "/auth/signin";

        $params = array(
            "login" => $this->login,
            "passwd" => $this->passwd
        );

        $this->account = $this->request($path, "POST", $params);

        return $this->account;
    }

    public function logout() {
        if (! $this->account)
            return;

        $path = "/logout";
        $response = $this->request($path, "GET");
        $this->account = NULL;
        $this->cookie = NULL;
    }

    public function status($check_id) {
        $path = "/status/" . $check_id;

        return $this->request($path, "GET");
    }

    public function status_logs($check_id) {
        $path = "/status/logs/" . $check_id;

        return $this->request($path, "GET");
    }

    public function status_metrics($check_id, $timewindow=NULL) {
        if ($timewindow)
            $path = "/status/metrics/" . $timewindow . "/" . $check_id;
        else
            $path = "/status/metrics/" . $check_id;

        return $this->request($path, "GET");
    }

    public function checks() {
        $this->signin();

        $path = "/checks";
        return $this->request($path, "GET");
    }

    public function check($check_id) {
        $this->signin();

        $path = "/checks/" . $check_id;
        return $this->request($path, "GET");
    }

    public function check_create($data) {
        $this->signin();

        $path = "/checks";
        return $this->request($path, "POST", $data);
    }

    public function check_update($check_id, $data) {
        $this->signin();

        $path = "/checks/" . $check_id;
        return $this->request($path, "POST", $data);
    }

    public function check_delete($check_id) {
        $this->signin();

        $path = "/checks/" . $check_id;
        return $this->request($path, "DELETE");
    }
}

?>