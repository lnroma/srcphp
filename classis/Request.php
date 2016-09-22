<?php

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 21.09.16
 * Time: 15:27
 */
class Request
{
    private $_token = null;
    private $_href = null;
    private $_method = null;
    private $_filePath = null;
    private $_fileName = null;

    /**
     * get oauth link
     */
    public function getOauthLink()
    {
        /**
         * https://oauth.yandex.ru/authorize?
         * response_type=token
         * & client_id=<идентификатор приложения>
         * [& device_id=<идентификатор устройства>]
         * [& device_name=<имя устройства>]
         * [& display=popup]
         * [& login_hint=<имя пользователя или электронный адрес>]
         * [& force_confirm=yes]
         * [& state=<произвольная строка>]
         */
        $link = 'https://oauth.yandex.ru/authorize'
            .'?response_type=token'
            . '&client_id=8fc231e60575439fafcdb3b9281778a3';
        echo $link;
    }

    /**
     * set file path on disk
     * @param $filePath
     * @return $this
     */
    public function setFileNameOnDisk($name)
    {
        /**
         * https://cloud-api.yandex.net/v1/disk/resources/upload ?
         * path=<путь, по которому следует загрузить файл>
         */
        $link = 'https://cloud-api.yandex.net/v1/disk/resources/upload?path='.urlencode('/'.trim($name,'/'));
        $response = file_get_contents($link,false,$this->_context('GET'));
        $responseAsArray = json_decode($response,true);
        $this->_href = $responseAsArray['href'];
        $this->_method = $responseAsArray['method'];
        $this->_fileName = $name;
        return $this;
    }

    /**
     * get path to file on local disk
     * @param $path
     * @return $this
     */
    public function setPathToFile($path) {
        $this->_filePath = $path;
        return $this;
    }

    /**
     * upload file to disk
     */
    public function upload()
    {
        $ch = curl_init($this->_href);

        curl_setopt($ch,CURLOPT_HTTPHEADER,
            array(
                'Authorization',
                'OAuth '.$this->_token
            )
        );

        curl_setopt($ch,CURLOPT_INFILE,fopen($this->_filePath,"r"));
        curl_setopt($ch,CURLOPT_INFILESIZE,filesize($this->_filePath));
        curl_setopt($ch,CURLOPT_PUT,true);

        curl_exec($ch);
        curl_close($ch);
        return $this;
    }

    /**
     * public file and get public url for screenshot
     * @return mixed
     */
    public function publicateFile()
    {
        /**
         * https://cloud-api.yandex.net/v1/disk/resources/publish ?
         * path=<путь к публикуемому ресурсу>
         */
        $link = 'https://cloud-api.yandex.net/v1/disk/resources/publish?path='.urlencode('/'.trim($this->_fileName,'/'));
        $response = file_get_contents($link,false,$this->_context('PUT'));
        $responseAsArray = json_decode($response,true);
        $publicateFile = file_get_contents($responseAsArray['href'],false,$this->_context($responseAsArray['method']));
        $publicateFileAsArray = json_decode($publicateFile,true);
        return $publicateFileAsArray;
    }

    /**
     * set oauth token
     * @param $key
     * @return $this
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    /**
     * get context for request by file_get_contents
     * @param $method
     * @return resource
     */
    private function _context($method)
    {
        /**
         * Authorization: OAuth <key>
         */
        $opts = array(
            'http'=>array(
                'method'=>$method,
                'header'=>"Authorization: OAuth ".$this->_token."\r\n"
            )
        );

        $context = stream_context_create($opts);
        return $context;
    }

}