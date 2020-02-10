<?php

namespace Fly\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlyUserBundle extends Bundle
{

    public function getParent()
    {
        return 'FOSUserBundle';
    }

    public static function NoticeTypeNames()
    {
        return [
            'friendRequest',
            'groupInvite',
        ];
    }

    public static function getNoticeTypeFriends()
    {
        return [
            'name'=>'friendRequest',
            'msg'=>'Friendship request'
        ];
    }

    public static function getNoticeTypeGroupInvite()
    {
        return [
            'name'=>'groupInvite',
            'msg'=>'Group Invite'
        ];
    }

    public static function getNoticeTypes()
    {
        return [
            'friendRequest'=>[
                'title'=>'Friendship request',
                'msg'=>'Friendship request from %s'
            ],
            'groupInvite'=>[
                'title'=>'Group Invite',
                'msg'=>'%s has invited you to a group '
            ],
        ];

    }
}
