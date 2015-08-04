<?php

namespace Suffra;

use yii\base\UserException;
use yii\helpers\VarDumper;

class Config
{
    /**
     * Возвращает конфиг
     */
    public static function getConfig() {
        return require(__DIR__ . '/../config/suffra.php');
    }

    /**
     * Получить параметр конфигурации
     */
    public static function get($name) {
        return self::getConfig()[$name];
    }

    /**
     * Возвращает путь к проекту Suffra
     */
    public static function getBasePath() {
        return \yii\helpers\FileHelper::normalizePath(
            self::getConfig()['basePath']
        );
    }

    /**
     * Удаляет файл
     * Проверяет на существование файла перед удалением
     * @param string $path путь к файлу в проекте Suffra, например '/images/video/e35541f7de967b5cd40d7729c88accd3.jpg'
     */
    public static function deleteFile($path) {
        $basePath = self::getBasePath();
        $path = $basePath . $path;
        if (file_exists($path)) {
            if (!is_dir($path)) {
                unlink($path);
            }
        }
    }

    /**
     * Возвращает путь к директории пользователя по $userId
     * с созданием папки если надо и всеми подпапками
     *
     * @param integer $userId идентификатор пользователя
     *
     * @return string путь от корня сайта, например '/upload/users/1000/1'
     *
     * @throws \yii\base\UserException
     */
    public static function userDirectory($userId)
    {
        if ($userId <= 0) {
            throw new UserException('$userId <= 0');
        }
        $catalogs = [
            'avatars',
            'files',
            'media',
            'photos'
        ];
        $ucount = 1000;
        $grp = ((int)($userId / $ucount) + 1) * $ucount;
        $dir = "/upload/users/$grp/$userId";
        $res = $dir;
        $dir = self::getBasePath() . $dir;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);

            foreach ($catalogs as $cat) {
                mkdir($dir . '/' . $cat);
            }
            mkdir($dir . '/avatars/small');
        }

        return $res;
    }

    public static function createFullUrl($url) {
        $config =self::getConfig();
        $prefix = $config['isSsl'] ? 'https' : 'http';

        return $prefix . '://' . $config['serverName'] . $url;
    }
}