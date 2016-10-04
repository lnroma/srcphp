#!/usr/bin/php
<?php
require_once('classis/autoload.php');

class screenShoter {

    protected $_nameScreenshot = null;
    protected $_config = null;
    protected $_gtk = null;

    public function __construct()
    {
        $this->_gtk = new GtkPhp();
        $request = new Request();
        if(isset($argv[1]) && $argv[1] == '--getToken') {
            echo $request->getOauthLink();die;
        }

        $home = $_SERVER['HOME'];
        $this->_config = include($home . '/.config/scrphp/config.php');
        $this->_nameScreenshot = date('Y_m_d_G_i_s_') . 'screen.png';
        $this->scrot();
    }

    public function scrot() {
        $this->_gtk->alert("Здравствуйте, для того чтобы сделать скриншот нажмите Ok и выберите область, на экране");
        system('scrot -s /tmp/'.$this->_nameScreenshot);
        $resultPrev = $this->_gtk->preview('/tmp/'.$this->_nameScreenshot);
        if($resultPrev == 'upload') {
            $this->upload();
        } else {
            $this->scrot();
        }
    }

    public function upload() {
        $request = new Request();
        $result = $request
            ->setToken($this->_config['token'])
            ->setFileNameOnDisk($this->_nameScreenshot)
            ->setPathToFile('/tmp/'.$this->_nameScreenshot)
            ->upload()
            ->publicateFile();
        $url = $result['public_url'];
        $this->_gtk->alert("Ссылка на скрин:".$url);
    }

}

new screenShoter();