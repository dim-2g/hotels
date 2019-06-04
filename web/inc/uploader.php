<?php
$uploader = new KDUploader('http://html2.tophotels.site/');
$uploader->run();

class KDUploader
{
    /*
    * Донор
    */
    public $mainDomain;
    
    /*
    * Расширения файлов, которые будут скачиваться
    */
    public $downloadExtentions;
    
    /*
    * Информация о запрошенном адресе
    */
    public $urlInfo;
    
    /*
    * $_SERVER['REQUEST_URI']
    */
    public $requestUri;
    
    /*
    * полный адрес, включая протокол и домен
    */
    public $fullUrl;
    
    /*
    * адрес без QUERY_STRING
    */
    public $cleanUrl;
    
    public $extension;
    
    public function __construct($domain)
    {
        $this->downloadExtentions = [
            'jpg','jpeg','gif','png','css','js',
            'ttf','eot','woff','svg','ico','htc',
            'xls','xlsx','doc','docx','pdf','swf','flv','cur'
        ];
        $this->mainDomain = $this->normalizeDomain($domain);
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->fullUrl = $this->mainDomain . $this->requestUri;
        $this->cleanUrl = $this->removeQueryString($this->requestUri);
        $this->extension = $this->findExtension($this->cleanUrl);
    }
    
    public function run()
    {
        if (!$this->isAllowForDownload($this->extension)) {
            return;
        }
        if (!$donorContent = $this->curl($this->fullUrl)) {
            die('The remote content is empty');
        }
        //var_dump($this->findLocalPath($this->cleanUrl));
        //die('123');
        $this->createDirectories();
        file_put_contents($this->findLocalPath($this->cleanUrl), $donorContent);
        header('Location: ' . $this->requestUri);
    }
    
    public function findLocalPath($url)
    {
        return $_SERVER['DOCUMENT_ROOT'] . rawurldecode($url);
    }
    
    public function findExtension($url)
    {
        $pathInfo = pathinfo($url);
        return $pathInfo['extension'];
    }
    
    public function normalizeDomain($domain)
    {
        $domain = rtrim($domain, '/');
        return $domain;
    }
    
    private function createDirectories()
    {
        $directoryName = dirname($this->requestUri);
        $absoluteDirectoryName = $_SERVER['DOCUMENT_ROOT'] . $directoryName . '/';
        $absoluteDirectoryName = str_replace('//', '/', $absoluteDirectoryName);
        if (!file_exists($absoluteDirectoryName)) {
            mkdir($absoluteDirectoryName, 0755, true);    
        }
    }
    
    public function isAllowForDownload($extension)
    {
        return in_array(strtolower($extension), $this->downloadExtentions);
    }
    
    public function findUrlInfo($url)
    {
        return pathinfo($url);
    }
    
    public function removeQueryString($url)
    {
        return preg_replace('#(.*)\?.*$#', '$1', $url);
    }
    
    public function curl($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:25.0) Gecko/20100101 Firefox/25.0');
        $rawdata = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($http_code==200) {
            return $rawdata;    
        } else {
            return false;
        }  
    }
}