<?php

namespace Fly\PlatformBundle\Controller;


use Fly\PlatformBundle\Entity\Act;
use Fly\PlatformBundle\Entity\ActItem;
use Fly\PlatformBundle\Form\AccItemType;
use Fly\PlatformBundle\Form\AccType;
use Fly\PlatformBundle\Form\ActItemType;
use Fly\PlatformBundle\Form\ActType;
use Fly\UserBundle\Entity\Feed;
use Fly\UserBundle\Entity\FeedLike;
use Fly\UserBundle\Entity\FeedResource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class AjxController extends Controller
{
    /**
     * upload user profile image
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function uploadProfileImageAction(Request $request)
    {


        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return JsonResponse::create(['asc' => 'logout']);
        }

        $user = $this->getUser();


        $res = $this->get('fly.local.uploader')->uploadUserPicture($this->get('request')->files->get('file'), $user);
//        die('ok3');
        if ($res['asc'] == 'error') {
            return JsonResponse::create(['asc' => 'error', 'message' => $res['msg']]);
        }
        $userManager = $this->get('fos_user.user_manager');
        $user->setPhoto($res['url']);
        $user->setPhotoSmall($res['small_url']);

//        $userManager->createUser($user);
        $userManager->updateUser($user);

        return JsonResponse::create(['asc' => 'success', 'url' => $res['url'], 'small_url'=>$res['small_url']]);
//        return $this->render('FlyPlatformBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * upload user profile image
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function uploadProfileCoverAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return JsonResponse::create(['asc' => 'logout']);
        }

        $user = $this->getUser();


        $res = $this->get('fly.local.uploader')->uploadUserCover($this->get('request')->files->get('file'), $user);
        if ($res['asc'] == 'error') {
            return JsonResponse::create(['asc' => 'error', 'message' => $res['msg']]);
        }
        $userManager = $this->get('fos_user.user_manager');
        $user->setCover($res['url']);
//        $userManager->createUser($user);
        $userManager->updateUser($user);

        return JsonResponse::create(['asc' => 'success', 'url' => $res['url']]);
//        return $this->render('FlyPlatformBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * upload tmp file
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function uploadTmpFileAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return JsonResponse::create(['asc' => 'logout']);
        }

        $res = $this->get('fly.local.uploader')->upload($this->get('request')->files->get('file'));
        if ($res['asc'] == 'error') {
            return JsonResponse::create(['asc' => 'error', 'message' => $res['msg']]);
        }

        return JsonResponse::create(['asc' => 'success', 'url' => $res['url']]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function acceptInviteAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return JsonResponse::create(['asc' => 'unlogged']);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $group = $em->getRepository('FlyUserBundle:Group')->find($request->request->get('groupId'));
        if(!$group){
            return JsonResponse::create(['asc' => 'error', 'msg' => 'Group does not exist']);
        }
        $invite = $em->getRepository('FlyUserBundle:GroupInvitation')->findOneBy([
            'group' => $request->request->get('groupId'),
            'user' => $this->getUser()->getId()
        ]);
        if ($invite) {

            $this->getUser()->addGroup($group);
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($this->getUser());

            $em->remove($invite);
            $em->flush();


            return JsonResponse::create(['asc' => 'success']);
        }

        return JsonResponse::create(['asc' => 'error', 'msg' => 'Invitation does not exist']);


    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function declineInviteAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return JsonResponse::create(['asc' => 'unlogged']);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $group = $em->getRepository('FlyUserBundle:Group')->find($request->request->get('groupId'));
        if(!$group){
            return JsonResponse::create(['asc' => 'error', 'msg' => 'Group does not exist']);
        }
        $invite = $em->getRepository('FlyUserBundle:GroupInvitation')->findOneBy([
            'group' => $request->request->get('groupId'),
            'user' => $this->getUser()->getId()
        ]);
        if ($invite) {

            $em->remove($invite);
            $em->flush();

            return JsonResponse::create(['asc' => 'success']);
        }

        return JsonResponse::create(['asc' => 'error', 'msg' => 'Invitation does not exist']);


    }


    public function getUrlContentAction(Request $request)
    {

        if(!$request->isXmlHttpRequest()){
            return new Response('Request is not an XmlHttpRequest');
        }
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return JsonResponse::create(['asc' => 'unlogged']);
        }

        $url = $request->request->get('url');

        //check URL
        if(!$url){
            return JsonResponse::create(['asc' => 'error','msg'=>'URL is empty']);
        }

        $urlData = $this->get('fly.user.feed')->getUrlData($url);

        return JsonResponse::create($urlData);

    }


    /**
     * @param Request $request
     * @param $groupName
     */
    public function postFeedAction(Request $request, $groupName)
    {
        if(!$request->isXmlHttpRequest()){
            die();
        }
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return JsonResponse::create(['asc' => 'unlogged'],401);
        }
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('FlyUserBundle:Group')->findOneByName($groupName);
        if(!$group){
            return JsonResponse::create(['asc' => 'error','msg'=>'Group '.$groupName.' not found.'],200);
        }

        $feed = new Feed();
        $feed->setGroup($group);
        $feed->setUser($this->getUser());
        $feed->setDescription($request->request->get('feedContent'));

        $feedCategory = $request->request->get('feedCategory')?$request->request->get('feedCategory'):null;
        if($feedCategory){
            $cat = $em->getRepository('FlyUserBundle:FeedCategory')->find($feedCategory);
            if($cat){
                $feed->setFeedCategory($cat);
            }

        }
        $em->persist($feed);
        $em->flush();

        $resources = $request->request->get('feedResources');
        if(is_array($resources) && count($resources) > 0){
            foreach($resources as $resource){
                if($resource['type'] == 'uploadImage'){
                    $resource['image'] = $this->get('fly.local.uploader')->moveFeedImage($resource['tmpPath'],$group->getId());
                    $resource['resourceUrl'] = $resource['image'];
                }

                $feedRes = new FeedResource();
                $feedRes->setFeed($feed);
                $feedRes->setType($resource['type']);
                $feedRes->setTitle($resource['title']);
                $feedRes->setDescription($resource['description']);
                $feedRes->setImage($resource['image']);
                $feedRes->setThumb($resource['thumb']);
                $feedRes->setResourceUrl($resource['resourceUrl']);

                if($resource['type'] == 'youtube'){
                    $feedRes->setEmbedId($resource['embedId']);
                }

                $em->persist($feedRes);
                $em->flush();
            }
        }

        return JsonResponse::create(['asc' => 'success','msg'=>'ok'],200);

    }

    public function getFeedAction(Request $request, $groupName)
    {
        if(!$request->isXmlHttpRequest()){
            return new Response('Request is not an XmlHttpRequest');
        }
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return JsonResponse::create(['asc' => 'unlogged'],401);
        }
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('FlyUserBundle:Group')->findOneByName($groupName);
        if(!$group){
            return JsonResponse::create(['asc' => 'error','msg'=>'Group '.$groupName.' not found.'],200);
        }

        $lastId = $request->request->get('lastId',0);
        $refresh = $request->request->get('refresh',null);
        $lastTime = $request->request->get('lastTime',0);
        $feeds = $em->getRepository('FlyUserBundle:Feed')->findForGroup($group, $this->get('service_container')->getParameter('pagination_limit'), $lastId, $refresh, $lastTime );
        if(count($feeds)){
            if($refresh || $lastId == 0){
                $firstFeed = $feeds[0];
                $lastTime = $firstFeed->getCreated()->getTimestamp();
            }
            $lastFeed = $feeds[count($feeds)-1];
            $lastId = $lastFeed->getId();



        }

        $html = $this->renderView('@FlyUser/Group/feeds_block.html.twig',['feeds'=>$feeds]);
        $date = new \DateTime();

        return JsonResponse::create($this->jsnResponseData('success',['lastId'=>$lastId,'html'=>$html, 'lastTime' => $lastTime, 'refresh'=>$refresh]),200);

    }


    public function feedLikeAction(Request $request)
    {
        if(!$request->isXmlHttpRequest()){
            return new Response('Request is not an XmlHttpRequest');
        }
        if(!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return JsonResponse::create(['asc' => 'unlogged'],401);
        }

        $em = $this->getDoctrine()->getManager();
        $feedId = $request->request->get('feedId', 0);
        $action = 'like';

        $feed = $em->getRepository('FlyUserBundle:Feed')->find($feedId);
        if(!$feed){
            return JsonResponse::create(['asc' => 'error','msg'=>'Feed not found.'],200);
        }

        $like = $em->getRepository('FlyUserBundle:FeedLike')->findOneBy(['user'=>$this->getUser(), 'feed'=>$feed]);

        if(!$like){
            $like = new FeedLike();
            $like->setFeed($feed);
            $like->setUser($this->getUser());
            $em->persist($like);


        }else{
            $em->remove($like);
            $action = 'unlike';
        }
        $em->flush();

        return JsonResponse::create(['asc' => 'success','action'=>$action, 'feedId'=>$feed->getId(), 'stat'=>count($feed->getLike())],200);
    }

    public function createGroupAction(Request $request)
    {
        $steps = $request->request->get('steps');
        $stepForm = $this->get('fly.step.form');
        $stepForm->create($steps);

        return JsonResponse::create(['asc' => 'success','action'=>'createGroupAction'],200);
    }

    public function createGroupStepsAction(Request $request)
    {
        $stepForm = $this->get('fly.step.form');
        $steps = $stepForm->getSteps();

        return JsonResponse::create(['asc' => 'success', 'steps'=>$steps,'action'=>'createGroupStepsAction'],200);
    }

    /**
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response|static
     */
    public function checkGroupAction($name)
    {
        $group = $this->get('doctrine.orm.entity_manager')->getRepository('FlyUserBundle:Group')->findOneBy(['name'=>trim($name)]);
        if($group){
            $msg = 'Group with name "'.$name.'" always exist. Please choose another name for your Group';
            return JsonResponse::create(['asc' => 'error', 'msg'=>$msg],200);

        }
        return JsonResponse::create(['asc' => 'success'],200);

    }

    public function calendarEventsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('FlyPlatformBundle:AccItem')->getItemsByMonth();
        $accEntities = $em->getRepository('FlyPlatformBundle:AccItem')->getItemsByMonth();
        $actEntities = $em->getRepository('FlyPlatformBundle:ActItem')->getItemsByMonth();

        $entities = [];
        foreach($accEntities as $obj){
            $entities[] = $obj;
        }
        foreach($actEntities as $obj){
            $entities[] = $obj;
        }
        return JsonResponse::create(['asc'=>'success','data'=>$entities]);

    }

    public function calendarEventEditFormAction($id, $type)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $accId = null;
        $accFormView = null;
        $accItemFormView = null;
        $actFormView = null;

        if($type == 'accItem'){
            $entity = $em->getRepository('FlyPlatformBundle:AccItem')->find($id);
            if($entity){
                $accId = $entity->getAcc()->getId();
                $accItemForm = $this->createForm(new AccItemType(), $entity);
                $accForm = $this->createForm(new AccType());

                $accItemFormView = $this->render('FlyUserBundle:Package/form:accItemForm.html.twig',[
                    'accItemForm'=>$accItemForm->createView(),
                    'editAction'=>true,
                    'entityId'=>$id,
                ])->getContent();

                $accFormView = $this->render('FlyUserBundle:Package/form:accForm.html.twig',[
                    'accForm'=>$accForm->createView()
                ])->getContent();
            }
        }

        if($type == 'actItem'){
            $entity = $em->getRepository('FlyPlatformBundle:ActItem')->find($id);
            if($entity){
                $actForm = $this->createForm(new ActType(), $entity->getAct());

                $actFormView = $this->render('FlyUserBundle:Package/form:actForm.html.twig',[
                    'actForm'=>$actForm->createView(),
                    'editAction'=>true,
                    'entityId'=>$entity->getAct()->getId(),
                ])->getContent();

            }
        }


        return JsonResponse::create([
            'asc'=>'success',
            'action'=>'calendarEventEditFormAction',
            'type'=>$type,
            'id'=>$id,
            'accId'=>$accId,
            'date'=>[
                'checkin'=>$entity->getCheckin(),
                'checkout'=>$entity->getCheckout(),
            ],
            'forms'=>[
                'accFormView'=>$accFormView,
                'accItemFormView'=>$accItemFormView,
                'actFormView'=>$actFormView,
            ]
        ]);

    }

    public function calendarEventRemoveAction($id, $type)
    {

        $em = $this->get('doctrine.orm.entity_manager');
        $entity = null;
        if($type == 'accItem'){
            $entity = $em->getRepository('FlyPlatformBundle:AccItem')->find($id);
        }

        if($type == 'actItem'){
            $entity = $em->getRepository('FlyPlatformBundle:ActItem')->find($id);
        }

        if($entity){
            $em->remove($entity);
            $em->flush();
            return JsonResponse::create(['asc'=>'success']);
        }else{
            return JsonResponse::create(['asc'=>'error','msg'=>'Cant delete Package Item']);
        }

    }

    public function airportSearchAction(Request $request)
    {
        $q = $request->get("q");
        $page = $request->get('page');

        $res = $this->get('doctrine.orm.entity_manager')->getRepository('FlyPlatformBundle:Airport')->filters($q);
        return JsonResponse::create($res);
    }

    protected function jsnResponseData($asc,$params=[],$msg=null)
    {
        return ['asc'=>$asc, 'params'=>$params, 'msg'=>$msg];
    }



}
