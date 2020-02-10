<?php

namespace Fly\UserBundle\Controller;

use Fly\UserBundle\Entity\FeedComment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Feed controller.
 *
 */
class FeedCommentController extends Controller
{

   public function indexAction(Request $request)
   {

   }

   public function createAjxAction(Request $request)
   {
       // check is logged

       if(!$this->get('security.authorization_checker')->isGranted('ROLE_USER')){
           return JsonResponse::create(['asc'=>'error','msg'=>'Redirect to login'],401);
       }

       $em = $this->get('doctrine.orm.entity_manager');
        // check params
       if(!$request->request->get('params')){
           return JsonResponse::create(['asc'=>'error','msg'=>'Server Error(251)']);
       }

       $params = $request->request->get('params');

       // check comment content
       if(!isset($params['text']) || !$params['text'] ){
           return JsonResponse::create(['asc'=>'error','msg'=>'You try to post an empry comment. Please write comment.']);
       }

       // check feed
       if(!isset($params['feedId']) || !$params['feedId'] ){
           return JsonResponse::create(['asc'=>'error','msg'=>'Server Error(252)']);
       }

       $feed = $em->getRepository('FlyUserBundle:Feed')->find($params['feedId']);
       if(!$feed){
           return JsonResponse::create(['asc'=>'error','msg'=>'Server Error(253)']);
       }


       $comment = new FeedComment();
       $comment->setContent($params['text']);
       $comment->setFeed($feed);
       $comment->setUser($this->getUser());

//       $feedCommentRepository = $em->getRepository('FlyUserBundle:FeedComment');
//       $feedCommentRepository
//           ->persistAsFirstChild($comment);
       $em->persist($comment);
       $em->flush();

       return JsonResponse::create(['asc'=>'success','comment'=>$comment],200);
   }


    public function deleteAjxAction(Request $request)
    {
        // check is logged

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return JsonResponse::create(['asc' => 'error', 'msg' => 'Redirect to login'], 401);
        }

        // check method
        if($request->getMethod() != 'DELETE'){
            return JsonResponse::create(['asc'=>'error','msg'=>'Server Error.']);
        }

        $params = $request->request->get('params');

        // check comment
        if(!isset($params['id']) || !$params['id'] ){
            return JsonResponse::create(['asc'=>'error','msg'=>'Server Error(252)']);
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $comment = $em->getRepository('FlyUserBundle:FeedComment')->find($params['id']);

        if(!$comment){
            return JsonResponse::create(['asc'=>'error','msg'=>'Comment not found']);
        }

        if($comment->getUser() != $this->getUser()){
            return JsonResponse::create(['asc'=>'error','msg'=>'Server Error(253)']);
        }
        $commentId = $comment->getId();
        $em->remove($comment);
        $em->flush();

        return JsonResponse::create(['asc'=>'success','msg'=>'Comment was deleted', 'id'=>$commentId],200);

//        var_dump($request->getMethod());die;

    }

}
