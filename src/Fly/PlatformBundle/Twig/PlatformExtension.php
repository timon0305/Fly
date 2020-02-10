<?php
namespace Fly\PlatformBundle\Twig;

/**
 * Description of DoctorsoncallExtension
 *
 * @author ivan
 */
use Fly\UserBundle\Entity\User;
use Symfony\Component\Intl\Intl;
use Gregwar\Image\Image;
use Gregwar\ImageBundle\Services\ImageHandling;

class PlatformExtension extends \Twig_Extension
{
    protected $em;
    protected $breadcrumbs;
    protected $router;
    private $imageHandler;
    private $rootDir;

    public function __construct($breadcrumbs, $router, $em, ImageHandling $imageHandler)
    {
//        $this->em = $em;
        $this->breadcrumbs = $breadcrumbs;
        $this->router = $router;
        $this->em = $em;
        $this->imageHandler = $imageHandler;
        $this->rootDir = __DIR__.'/../../../../web';

    }

//    public function getFilters()
//    {
//        return array(
//            new \Twig_SimpleFilter('languages', array($this, 'getLanguages')),
//            new \Twig_SimpleFilter('gravatar', array($this, 'getGravatar')),
////            new \Twig_SimpleFilter('assetfile', array($this, 'assetfileFilter')),
////            new \Twig_SimpleFilter('mandatory_forms', array($this, 'mandatoryFormsFilter')),
////            new \Twig_SimpleFilter('optional_forms', array($this, 'optionalFormsFilter')),
//        );
//    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('breadcrumbs', array($this, 'getBreadcrumbs')),
            new \Twig_SimpleFunction('haveinvite', array($this, 'haveInvite')),
            new \Twig_SimpleFunction('isHaveLike', array($this, 'isHaveLike')),
            new \Twig_SimpleFunction('userPhoto', array($this, 'userPhoto')),
//            new \Twig_SimpleFunction('isCommentOwner', array($this, 'isCommentOwner')),
        );

    }

    public function getBreadcrumbs($slug,$pageTitle=null)
    {
        $barr = $this->breadcrumbs;
        $html = '<ol class="breadcrumb" style="margin-bottom: 5px;">';
        $menuReverse = array();
        if(isset($barr[$slug])){
            $menuReverse = $this->createBcArr($barr[$slug],$menuReverse,$barr);
            $menu = array_reverse($menuReverse);
            foreach($menu as $item){
                $html .= '<li><a href="'.(($item['url'])?$this->router->generate($item['url']):"#").'">'.$item['title'].'</a></li>';
            }
        }
        if($pageTitle){
            $html .= '<li class="active">'. $pageTitle.'</li>';
        }
        $html .= '</ol>';
        return $html;
    }

    protected function createBcArr($item, &$arr, $barr)
    {
        if(count($arr)){
            $arr[]=['title'=>$item['name'],'url'=>$item['url']];
        }else{
            $arr[]=['title'=>$item['name'],'url'=>null];
        }

        if($item['parent']){
            $this->createBcArr($barr[$item['parent']],$arr,$barr);
        }
        return $arr;
    }

    public function haveInvite($group, $user)
    {
        foreach($group->getInvitation() as $invite){
            if($invite->getUser() == $user && $invite->getSent() == 0){
                return true;
            }
        }
        return false;

    }

    public function isHaveLike($feed, $user)
    {
        $res = $this->em->getRepository('FlyUserBundle:FeedLike')->getByUserFeed($feed, $user);
        return count($res);
    }

    public function userPhoto(User $user, $w = null, $h = null)
    {
        $w = ($w)?$w:100;
        $h = ($h)?$h:100;
        $progfileImage = $user->getUserProfileImage();

        if($user->getFacebookUserImage() || $user->getTwitterUserImage()){
            $path =  $progfileImage;
        }else{

            $path = $this->imageHandler->open( $this->rootDir.$progfileImage)
                ->zoomCrop($w,$h,0,'center','center')
                ->jpeg();
        }

        return $path;
    }


    public function getName()
    {
        return 'fly_platform_extension';
    }
}
