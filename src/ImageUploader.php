<?php

namespace PwStarterKit\Tools;

/**
 * Class ImageUploader
 */
class ImageUploader
{

    /**
     * @param array $file
     * @param string $destination_dir
     * @return bool|string
     * @throws Exception
     */
    public function uploadImage(array $file, $destination_dir = './upload/')
    {
        if ($file['error'] == UPLOAD_ERR_OK){
            @mkdir($destination_dir . "/" . date('Ymd') . "/", 0777);
            $authorized_extensions = array('jpg', 'jpeg', 'png');

            if (!is_dir($destination_dir) || !is_writeable($destination_dir)) {
                throw new Exception("Le dossier de destination n'existe pas !");
            }

            $lastPos = strRChr($file['name'], ".");
            if ($lastPos !== false && in_array(strToLower(subStr($lastPos, 1)), $authorized_extensions)) {
                $destination_dir .= date('Ymd');
                $destination_file = uniqid();
                if (move_uploaded_file($file['tmp_name'], $destination_dir . '/' . $destination_file . '_origine.jpeg')) {
                    $path = date('Ymd') . "/$destination_file";

                    @unlink($destination_dir . $destination_file . '_redim.jpg');
                    return $path;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * From php doc - created for multiple upload
     *
     * @param $file_post
     * @return array
     */
    public function reArrayFiles(&$file_post)
    {
        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }
}