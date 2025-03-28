<?php

namespace EasyWeChat\Kernel\Form;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\Part\DataPart;

class File extends DataPart
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public static function withContents(string $contents, ?string $filename = null, ?string $contentType = null, ?string $encoding = null): DataPart
    {
        if (null === $contentType) {
            $mimeTypes = new MimeTypes();
            ;

            if ($filename) {
                $ext = \strtolower(\pathinfo($filename, \PATHINFO_EXTENSION));
                $contentType = $mimeTypes->getMimeTypes($ext)[0] ?? 'application/octet-stream';
            } else {
                $tmp = \tempnam(\sys_get_temp_dir(), 'easywechat');
                if (!$tmp) {
                    throw new RuntimeException('Failed to create temporary file.');
                }

                \file_put_contents($tmp, $contents);
                $contentType = $mimeTypes->guessMimeType($tmp) ?? 'application/octet-stream';
                $filename = \md5($contents) .'.'. ($mimeTypes->getExtensions($contentType)[0] ?? null);
            }
        }

        return new self($contents, $filename, $contentType, $encoding);
    }
}
