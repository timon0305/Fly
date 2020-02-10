<?php

namespace Fly\UserBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gaufrette\Filesystem;

class Feed
{
    private $browser;
    private $domain;
    private static $allowedMimeTypes = array(
        'image/jpeg',
        'image/png',
        'image/gif'
    );

    private static $allowedImageExt = array(
        'jpeg',
        'png',
        'gif',
        'svg',
        'jpg'
    );

    const TYPE_YOUTUBE = 'youtube';
    const TYPE_VIMEO = 'vimeo';
    const TYPE_IMAGE = 'image';
    const TYPE_PAGE = 'page';

    private $feedTitle = '';
    private $feedDescr = '';
    private $feedThumb = '';
    private $feedImage = '';
    private $feedResourceUrl = '';
    private $feedType = '';


    private $feedData = [
        'type' =>'',
        'title' => '',
        'description' => '',
        'image'=>'',
        'thumb'=>'',
        'resourceUrl'=> ''
    ];

    private $urlData = ['asc' => 'error',"msg"=>'Some things broken'];

    public function __construct($browser)
    {
        $this->browser = $browser;
    }

    public function getUrlData($url){

        $urlExplode = explode('.',$url);
        if(in_array(end($urlExplode),self::$allowedImageExt)){
            $this->feedData['type'] = self::TYPE_IMAGE;
            $this->feedData['resourceUrl'] = $url;
            $this->feedData['image'] = $url;

            $this->urlData = ['asc' => 'success','type'=>self::TYPE_IMAGE, 'data'=>$this->feedData];
        }else{
            $this->urlData = $this->getDataByUrl($url);
        }

        return $this->urlData;
    }



    public function getDataByUrl($url)
    {
        $res = ['asc' => 'error',"msg"=>'Some things broken'];
        $this->domain =  parse_url($url, PHP_URL_HOST);
//        var_dump($domain,$url);
        switch($this->domain){
            case "www.youtube.com"  :
                $res = $this->getYoutubeInfo($url);
                break;
            case 'youtu.be' :
                $res = $this->getYoutubeInfo($url);
                break;
            case "vimeo.com" :
                $res = $this->getVimeoInfo($url);
                break;
            default:
                $res = $this->getPageInfo($url);
                break;
        }
//        var_dump($res);die;
        return $res;


    }

    public function getYoutubeInfo($url)
    {
        $this->feedData['type'] = self::TYPE_YOUTUBE;
        $this->feedData['resourceUrl']=$url;

        $apiKey = 'AIzaSyACTo9XghCjc8xPSNbS0oVB4tpcadmSFLo';

        $urlEx = ($this->domain == 'www.youtube.com')?explode('=',$url):explode('/',$url);
        $id = end($urlEx);

        $youtubeApiUrl = 'https://www.googleapis.com/youtube/v3/videos?id='.$id.'&key='.$apiKey.'&part=snippet,contentDetails,statistics,status';
        $response = $this->browser->get($youtubeApiUrl);
        $headers = $response->getHeaders();
        if(strpos($headers[0],'200')){
         $arr = json_decode($response->getContent(), true);
         $embedId =  $arr['items'][0]['id'];
         $data =  $arr['items'][0]['snippet'];
//            var_dump($data);die;
         $this->feedData['title'] = $data['title'];
         $this->feedData['description'] = $data['description'];
         $this->feedData['thumb'] = $data['thumbnails']['medium']['url'];
         $this->feedData['image'] = $data['thumbnails']['high']['url'];
         $this->feedData['embedId'] = $embedId;

            return ['asc'=>'success', 'type'=>self::TYPE_YOUTUBE, 'data'=>$this->feedData];


        }else{
            return ['asc'=>'error', 'msg'=>'Can`t get Youtube resource', 'type'=>self::TYPE_YOUTUBE, 'data'=>$this->feedData];
        }
//        return ['asc'=>'success', 'type'=>self::TYPE_YOUTUBE, 'data'=>$this->feedData];
    }

    public function getVimeoInfo($url)
    {
        $this->feedData['type'] = self::TYPE_VIMEO;

        return ['asc'=>'success', 'type'=>self::TYPE_VIMEO, 'data'=>$this->feedData];
    }

    public function getPageInfo($url)
    {
        $this->feedData['resourceUrl'] = $url;
        $this->feedData['type'] = self::TYPE_PAGE;
        $response = $this->browser->get($url);
        $html = $response->getContent();
        // get title
        preg_match("/<title>(.+)<\/title>/i", $html, $matches);
        if(preg_match("/<title>(.+)<\/title>/i", $html, $matches)){
            $this->feedData['title'] = $matches[1];
        }
        // descritplion
        if(preg_match("/<meta[^>]*name=[\"|\']description[\"|\'][^>]*content=[\"]([^\"]*)[\"][^>]*>/i", $html, $matches)){
            $this->feedData['description'] = $matches[1];
        }

        // get og:image
        if(preg_match('~<\s*meta\s+property="(og:image+)"\s+content="([^"]*)~i', $html, $matches)
            || preg_match('~<\s*meta\s+property=\'(og:image+)\'\s+content="([^"]*)~i', $html, $matches) ){
            $this->feedData['image'] = $matches[2];
        }

        if($this->feedData['image']){
            if( false === strpos($this->feedData['image'],'http')){
                $this->feedData['image'] = 'http://'.$this->domain.$this->feedData['image'];

            }
        }

        return ['asc'=>'success', 'type'=>self::TYPE_PAGE, 'data'=>$this->feedData];

    }

}