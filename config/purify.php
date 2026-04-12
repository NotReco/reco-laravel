<?php

use Stevebauman\Purify\Definitions\Html5Definition;

return [

    'default' => 'default',

    'configs' => [

        'default' => [
            'Core.Encoding' => 'utf-8',
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => implode(',', [
                'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
                'b', 'u', 'strong', 'i', 'em', 's', 'del', 'sub', 'sup', 'mark',
                'a[href|title|target|rel]',
                'ul', 'ol', 'li',
                'p[style|class]',
                'br',
                'span[style|class]',
                'img[width|height|alt|src|class|style]',
                'blockquote',
                'figure',
                'figcaption',
                'div[style|class]',
                'section',
                'article',
                'iframe[src|width|height|frameborder|title|class|style]',
                'video[src|width|height|class|style]',
                'source[src|type]',
            ]),
            'HTML.ForbiddenElements' => '',
            'CSS.AllowedProperties' => implode(',', [
                'font', 'font-size', 'font-weight', 'font-style', 'font-family',
                'text-decoration', 'padding-left', 'padding-right', 'margin-left', 'margin-right',
                'color', 'background-color', 'text-align', 'max-width', 'width', 'height',
            ]),
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//(www\\.youtube\\.com/embed/|www\\.youtube-nocookie\\.com/embed/|player\\.vimeo\\.com/video/)%i',
            'Attr.AllowedFrameTargets' => ['_blank'],
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => false,
        ],

    ],

    'definitions' => Html5Definition::class,

    'css-definitions' => null,

    'serializer' => [
        'driver' => env('CACHE_STORE', env('CACHE_DRIVER', 'file')),
        'cache' => \Stevebauman\Purify\Cache\CacheDefinitionCache::class,
    ],

];
