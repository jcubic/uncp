<?php
/*
 *  _   _ _ __   ___ _ __
 * | | | | '_ \ / __| '_ \
 * | |_| | | | | (__| |_) |
 *  \__,_|_| |_|\___| .__/
 *                  |_|
 *
 * UNsplash Cache Proxy
 *
 * Copyright (C) 2022 Jakub T. Jankiewicz <https://jcubic.pl/me>
 * Released under license GPLv3 or later
 *
 */

class RequestCache {
    function __construct($config) {
        $this->config = $config;
        $pristine = !is_file($this->config->CACHE_FILE);
        $this->db = new PDO('sqlite:' . $this->config->CACHE_FILE);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($pristine) {
            $this->query("CREATE TABLE cache(
                             time DATETIME DEFAULT CURRENT_TIMESTAMP,
                             url VARCHAR(200),
                             code INTEGER,
                             data Text
                         )");
        } else {
            $this->clean();
        }
    }

    public function fetch($url) {
        $cache = $this->cache($url);
        if (count($cache)) {
            return (object)[
                "body" => $cache[0]['data'],
                "code" => 200
            ];
        }
        $response = $this->curl($url);
        $this->store($url, $response);
        return $response;

    }

    private function cache($url) {
        return $this->query("SELECT data
                             FROM cache
                             WHERE url = ?", array($url));
    }

    private function clean() {
        $data = array($this->config->CACHE_TIME / 60 / 24);
        $this->query("DELETE FROM cache WHERE
                      (julianday(CURRENT_TIMESTAMP) - julianday(time)) > ?", $data);
    }

    private function store($url, $reponse) {
        $data = array(
            $url,
            $reponse->code,
            $reponse->body,
        );
        $query = "INSERT INTO cache (url, code, data) VALUES(?, ?, ?)";
        return $this->query($query, $data) == 1;
    }

    private function curl($url)  {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSH_COMPRESSION, true);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ]);
        $result = curl_exec($ch);
        $result = $result;
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return (object)[
            "body" => $result,
            "code" => $code
        ];
    }

    private function query($query, $data = null) {
        if ($data == null) {
            $res = $this->db->query($query);
        } else {
            $res = $this->db->prepare($query);
            if ($res) {
                if (!$res->execute($data)) {
                    throw Exception("execute query failed");
                }
            } else {
                throw Exception("wrong query");
            }
        }
        if ($res) {
            if (preg_match("/^\s*INSERT|UPDATE|DELETE|ALTER|CREATE|DROP/i", $query)) {
                return $res->rowCount();
            } else {
                return $res->fetchAll(PDO::FETCH_ASSOC);
            }
        } else {
            throw new Exception("Coudn't open file");
        }
    }
}

function request() {
    if (!isset($_GET['q'])) {
        return invalid_response();
    } else {
        $config = (object)json_decode(file_get_contents('config.json'));
        $base = 'https://api.unsplash.com/search/photos';
        $url = $base . "?" . http_build_query(array(
            'query' => $_GET['q'],
            'per_page' => isset($_GET['s']) ? $_GET['s'] : 10,
            'client_id' => $config->ACCESS_KEY
        ));
        $cache = new RequestCache($config);
        return $cache->fetch($url);
    }
}

function invalid_response() {
    return (object)[
        'body' => '{"error": "Invaid Request"}',
        'code' => 400
    ];
}

function serve($response) {
    global $statuses;
    $code = $response->code;
    if ($code != 200) {
        error($code);
    }
    echo $response->body;
}

function error($code) {
    if (isset($statuses[$code])) {
        header("HTTP/1.0 $code {$statuses[$code]}");
    }
}

$statuses = array(
    500 => "Internal Server Error",
    400 => "Bad Request",
    403 => "Forbidden",
    404 => "Not Found",
    204 => "No Content"
);

// main
header_remove("X-Powered-By");
header('Content-Type: application/javascript');

$req = request();
serve($req);
