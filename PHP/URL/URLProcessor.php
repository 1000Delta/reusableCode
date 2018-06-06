<?php
/**
 * URLProcessor --基于curl的url处理类
 *
 */

namespace MyClass\URL;

class URLProcessor {

    const GET = 0;
    const POST = 1;

    private
        $url = '',
        $data = '',
        $method = -1,
        $response = '';

    public function __construct(string $url) {

        $this->url = $url;
    }

    public function showUrl() {

        return $this->url;
    }

    private function setData(array $data) {

        $str = '';
        $key0 = key($data);
        foreach ($data as $param => $value) {

            if ($key0 !== $param) $str .= '&';
            $str .= $param.'='.$value;
        }

        $this->data = $str;
    }

    public function get(array $data) {

        $this->method = URLProcessor::GET;
        $this->setData($data);

        return $this->url.'?'.$this->data;
    }

    public function post(array $data) {

        $this->method = URLProcessor::POST;
        $this->setData($data);

        return $this->url;
    }

    public function exec() {

        if ($this->method === URLProcessor::GET) {

            $ch = curl_init($this->url.'?'.$this->data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, false);
            $this->response = curl_exec($ch);

            return $this->response;
        } elseif ($this->method = URLProcessor::POST) {

            $ch = curl_init($this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, false);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);

            return $this->response = curl_exec($ch);
        } else {

            return -1;
        }

    }
}