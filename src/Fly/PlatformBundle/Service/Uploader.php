<?php

namespace Fly\PlatformBundle\Service;

use Gregwar\ImageBundle\Services\ImageHandling;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gaufrette\Filesystem;

class Uploader
{
    private static $allowedMimeTypes = array(
        'image/jpeg',
        'image/png',
        'image/gif'
    );

    private $filesystem;
    private $imageHandler;
    private $rootDir;

    public function __construct(Filesystem $filesystem, ImageHandling $imageHandler)
    {
        $this->filesystem = $filesystem;
        $this->imageHandler = $imageHandler;
        $this->rootDir = __DIR__.'/../../../../web/uploads';
    }

    public function uploadUserPicture(UploadedFile $file, $user){
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes)) {
            return ['asc'=>'error','msg'=>sprintf('Files of type %s are not allowed.',
                $file->getClientMimeType())];
        }

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s/%s/%s.%s', $user->getUsername(), 'profile', 'profile-picture',
            $file->getClientOriginalExtension());
        $filenameSm = sprintf('%s/%s/%s.%s', $user->getUsername(), 'profile', 'profile-picture_sm',
            $file->getClientOriginalExtension());

        $adapter = $this->filesystem->getAdapter();
        $adapter->write($filename, file_get_contents($file->getPathname()));

        $src = $this->rootDir.DIRECTORY_SEPARATOR.$user->getUsername().DIRECTORY_SEPARATOR.'profile'.DIRECTORY_SEPARATOR.'profile-picture.'.$file->getClientOriginalExtension();
        $target = $this->rootDir.DIRECTORY_SEPARATOR.$user->getUsername().DIRECTORY_SEPARATOR.'profile'.DIRECTORY_SEPARATOR.'profile-picture_sm.'.$file->getClientOriginalExtension();

        $this->imageHandler->open($src)
                ->zoomCrop(60,60,0,'center','center')
                ->save($target, 'jpg', 100)
        ;
        return ['asc'=>'success','url'=>$filename, 'small_url'=>$filenameSm];
    }

    public function uploadUserCover(UploadedFile $file, $user){
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes)) {
            return ['asc'=>'error','msg'=>sprintf('Files of type %s are not allowed.',
                $file->getClientMimeType())];
        }

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s/%s/%s.%s', $user->getUsername(), 'profile', 'cover-picture'.time(),
            $file->getClientOriginalExtension());

        $adapter = $this->filesystem->getAdapter();
        $adapter->write($filename, file_get_contents($file->getPathname()));

        return ['asc'=>'success','url'=>$filename];
    }

    public function uploadAccImage(UploadedFile $file){
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes)) {
            return ['asc'=>'error','msg'=>sprintf('Files of type %s are not allowed.',
                $file->getClientMimeType())];
        }

        $uid = uniqid(md5(time()));
        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('%s/%s.%s', 'accomodation', $uid,
            $file->getClientOriginalExtension());

        $adapter = $this->filesystem->getAdapter();
        $adapter->write($filename, file_get_contents($file->getPathname()));

        return ['asc'=>'success','url'=>$filename];
    }

    public function upload(UploadedFile $file)
    {
        // Check if the file's mime type is in the list of allowed mime types.
        if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes)) {
            return ['asc'=>'error','msg'=>sprintf('Files of type %s are not allowed.',
                $file->getClientMimeType())];
        }

        // Generate a unique filename based on the date and add file extension of the uploaded file
        $filename = sprintf('tmp/%s/%s/%s/%s.%s', date('Y'), date('m'), date('d'), uniqid(),
            $file->getClientOriginalExtension());

        $adapter = $this->filesystem->getAdapter();
//        var_dump($adapter);die;
//        $adapter->setMetadata($filename, array('contentType' => $file->getClientMimeType()));
        $adapter->write($filename, file_get_contents($file->getPathname()));

        return ['asc'=>'success','url'=>$filename];
    }

    public function uploadFile($formData, $entity)
    {
        if ($formData->getPicture() instanceof UploadedFile) {
            $file = $formData->getPicture();
            // Check if the file's mime type is in the list of allowed mime types.
            if (!in_array($file->getClientMimeType(), self::$allowedMimeTypes)) {
                throw new \InvalidArgumentException(sprintf('Files of type %s are not allowed.',
                    $file->getClientMimeType()));
            }
            // Generate a unique filename based on the date and add file extension of the uploaded file
            $filename = sprintf('%s/%s/%s/%s.%s', date('Y'), date('m'), date('d'), uniqid(),
                $file->getClientOriginalExtension());

            $adapter = $this->filesystem->getAdapter();

//            if ($entity->getPicture()) {
//                var_dump($entity->getPicture());
//                $adapter->delete($entity->getPicture());
//            }

            $adapter->write($filename, file_get_contents($file->getPathname()));

            $entity->setPicture($filename);

            return true;
        }

    }

    public function moveFeedImage($path,$groupId){
        $arrPath = explode('/',$path);
        $webPath = '/uploads/feeds/'.$groupId;
        $srcWebPath = __DIR__.'/../../../../web';
        if(!file_exists($srcWebPath.$webPath)){
            mkdir($srcWebPath.$webPath,0777,true);
        }
        $newPath = $srcWebPath.$webPath.'/'.end($arrPath);
        copy($srcWebPath.$path,$newPath);
        unlink($srcWebPath.$path);
        return $webPath.'/'.end($arrPath);
    }
}

///var/www/fly40/src/Fly/PlatformBundle/Service/Uploader.php