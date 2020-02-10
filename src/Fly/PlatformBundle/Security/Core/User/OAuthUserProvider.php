<?php
namespace Fly\PlatformBundle\Security\Core\User;



use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * Class OAuthUserProvider
 * @package AppBundle\Security\Core\User
 */
class OAuthUserProvider extends BaseClass
{
    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
//        var_dump($response->getResponse());die;
        $socialID = null;
        $email = null;
        $username = null;
        $firstname = '';
        $lastname = '';
        $userImage = null;
        $socialData = $response->getResponse();
        $user_location = '';
        $user_hometown = '';

        //then set its corresponding social id
        $service = $response->getResourceOwner()->getName();

        switch($service){
            case 'google':
                // $user->setGoogleId($socialID);
                break;
            case 'facebook':
                $socialID = $response->getUsername();
                $email = $response->getEmail() ?  $response->getEmail() : $response->getUsername().'@facebook.com';
                $username = $response->getEmail() ?  $response->getEmail() : $response->getUsername().'@facebook.com';
                list($firstname, $lastname) = explode(' ',$response->getNickname());
                $userImage = isset($socialData['picture']['data']['url']) ? $socialData['picture']['data']['url'] : '';
                $user_hometown = isset($socialData['user_hometown'])?$socialData['location']:null;
                $user_location = isset($socialData['user_location'])?$socialData['location']:null;

                break;
            case 'twitter':
                $socialID = $socialData['id_str'];
                $email = $response->getEmail() ?  $response->getEmail() : $socialData['id_str'].'@twitter.com';
                $username = $socialData['screen_name'];
                list($firstname, $lastname) = explode(' ',$socialData['name']);
                $userImage = isset($socialData['profile_image_url']) ? $socialData['profile_image_url'] : '';
                $user_location = isset($socialData['location'])?$socialData['location']:null;
                break;
        }

//        var_dump(
//            $service,
//            $socialID,
//            $email,
//            $username,
//            $firstname,
//            $lastname,
//            $socialData
//            );
//        die();
        $user = $this->userManager->findUserBy(array($this->getProperty($response)=>$socialID));
        //check if the user already has the corresponding social account
        if (null === $user) {
            //check if the user has a normal account
            $user = $this->userManager->findUserByEmail($email);

            if (null === $user || !$user instanceof UserInterface) {
                //if the user does not have a normal account, set it up:
                $user = $this->userManager->createUser();
                $user->setEmail($email);
                $user->setUsername($email);
                $user->setFirstName($firstname);
                $user->setLastName($lastname);
                $user->setPlainPassword(md5(uniqid()));
                $user->setEnabled(true);
            }

            switch ($service) {
                case 'google':
                   // $user->setGoogleId($socialID);
                    break;
                case 'facebook':
                    $user->setFacebookId($socialID);
                    $user->setFacebookUserImage($userImage);
                    $user->setHometownFb($user_hometown);
                    $user->setLocationFb($user_location);
                    break;
                case 'twitter':
                    $user->setTwitterId($socialID);
                    $user->setTwitterUserImage($userImage);
                    $user->setLocationTw($user_location);
                    break;
            }
            $this->userManager->updateUser($user);
        } else {
            //and then login the user
            $checker = new UserChecker();
            $checker->checkPreAuth($user);
        }

        return $user;
    }


}