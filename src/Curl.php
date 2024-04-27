<?php

declare(strict_types=1);
/***
 * Date 22.04.2023
 * @author zeroc0de <98693638+zeroc0de2022@users.noreply.github.com>
 */

namespace Routim;

/**
 * Curl
 * @noinspection PhpUnused
 */
class Curl
{
    /**
     * CURL settings received from the user
     * @var array
     */
    private array $settings;

    /**
     * CURL request options
     * @var array
     */
    private array $curlOptions;

    /**
     * Initialize CURL options by default
     * @return void
     */
    private function init(): void
    {
        $this->curlOptions = [CURLOPT_FAILONERROR    => true,
                              CURLOPT_RETURNTRANSFER => 1,
                              CURLOPT_SSL_VERIFYPEER => false,
                              CURLOPT_SSL_VERIFYHOST => false];
    }

    /**
     * For sending Curl request and getting result
     * @param array $settings []
     * @return array
     *  'status'  => (boolean)
     *  'headers' => (array)
     *  'body'    => (string)
     * @noinspection PhpUnused
     */
    public function request(array $settings): array
    {
        $this->init();
        $this->settings = $settings;
        foreach($this->settings as $key => $value) {
            $this->curlOptions += $this->getCurlOption($key, $value);
        }
        $ch1 = curl_init();
        curl_setopt_array($ch1, $this->curlOptions);
        $response = $this->handleError($ch1);
        if(!is_array($response)) {
            $response = $this->curlResponse($response);
        }
        return $response;
    }

    /**
     * For sending and handling possible CURL request errors
     * @param $ch1 - curl handler
     * @return string|array|bool
     */
    private function handleError($ch1): string|array|bool
    {
        $response = curl_exec($ch1);
        $errno    = curl_errno($ch1);
        if($errno) {
            $errorMessage = curl_error($ch1);
            return ['status' => false,
                    'body'   => $errorMessage];
        }
        // Empty response handler
        if($response === false) {
            return ['status' => false,
                    'body'   => curl_error($ch1)];
        }
        if(strlen($response) < 1) {
            return ['status' => false,
                    'body'   => 'EMPTY_RESPONSE'];
        }
        // Close connection
        curl_close($ch1);
        if(isset($this->settings['iconv'])) {
            $from     = $this->settings['iconv']['from'];
            $onto     = $this->settings['iconv']['to'];
            $response = iconv($from, $onto, $response);
        }
        return $response;
    }

    /***
     * Prepare return array with final results of request execution
     * @param $response - response from server
     * @return array
     */
    private function curlResponse($response): array
    {
        $headers = '';
        if(isset($this->settings['header']) && $this->settings['header'] == 1 && str_contains("\r\n\r\n", $response))
        {
            [$headers] = explode("\r\n\r\n", $response);
        }
        $data = str_replace($headers, '', $response);
        return ['status'  => true,
                'headers' => $headers,
                'body'    => trim($data)];
    }

    /**
     * Returns the specified option for CURL
     * @param $key -key of option
     * @param $value -value of option
     * @return array
     */
    private function getCurlOption($key, $value): array
    {
        return match ($key) {
            'url'        => [CURLOPT_URL => $value],                          // request url
            'useragent'  => [CURLOPT_USERAGENT => $value],                    // useragent
            'session'    => [CURLOPT_COOKIESESSION => $value],                // new session
            'cookie'     => [CURLOPT_COOKIE => $value],                       // cookie
            'header'     => [CURLOPT_HEADER => $value],                       // return headers
            'headers'    => [CURLOPT_HTTPHEADER => $value],                   // set headers
            'referer'    => [CURLOPT_REFERER => $value],                      // referer
            'nobody'     => [CURLOPT_NOBODY => $value],                       // return only headers
            'return'     => [CURLOPT_RETURNTRANSFER => $value],               // return result
            'follow'     => [CURLOPT_FOLLOWLOCATION => $value],               // follow redirects
            'timeout'    => [CURLOPT_TIMEOUT => $value],                      // timeout in seconds
            'timeout_mc' => [CURLOPT_TIMEOUT_MS => $value],                   // timeout in ms
            'proxy'      => $this->proxyForCurl($this->settings['proxy']),    // proxy
            'post'       => [CURLOPT_POSTFIELDS => $value,
                             CURLOPT_POST       => true],                     // post data/request
            'cookieFile' => [CURLOPT_COOKIEJAR  => $value,
                             CURLOPT_COOKIEFILE => $value],                   // cookie file
            default      => []
        };
    }

    /**
     * Returns an array with proxy options for CURL
     * @param $proxy - proxy settings
     * @return array
     */
    private function proxyForCurl(array $proxy): array
    {
        $output = [];
        if(isset($proxy['ip'])) {
            $proxyParts = explode(':', $proxy['ip']);
            $output     = [CURLOPT_PROXY     => $proxyParts[0] . ':' . $proxyParts[1],
                           CURLOPT_PROXYTYPE => constant('CURLPROXY_' . strtoupper($proxy['type'])) ?? CURLPROXY_HTTP];
            if(isset($proxyParts[2], $proxyParts[3])) {
                $output[CURLOPT_PROXYUSERPWD] = $proxyParts[2] . ':' . $proxyParts[3];
            }
        }
        return $output;
    }
}
