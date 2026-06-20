<?php

return [
    'encoding'         => 'UTF-8',
    'finalize'         => true,
    'ignoreNonStrings' => false,
    'cachePath'        => storage_path('app/purifier'),
    'cacheFileMode'    => 0755,

    'settings' => [
        /*
        | Default profile (kept conservative). Used by clean() when no
        | profile is named.
        */
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p,br,span,img[width|height|alt|src]',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,text-decoration,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => true,
        ],

        /*
        | Article body profile — matches what the rich-text editor and the
        | seeded content actually produce: headings, blockquotes, lists,
        | links, images, and the styled "callout" div. javascript:/data:
        | URIs and <script>/<style>/event handlers are stripped by
        | HTMLPurifier regardless of this allowlist.
        */
        'article' => [
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' =>
                'p,br,hr,'
                . 'h1,h2,h3,h4,'
                . 'strong,b,em,i,u,s,'
                . 'blockquote,pre,code,'
                . 'ul,ol,li,'
                . 'a[href|title|target|rel],'
                . 'img[src|alt|title|width|height],'
                . 'div[class|style],span[class|style]',
            'CSS.AllowedProperties' =>
                'background,background-color,border,border-radius,'
                . 'padding,margin,color,text-align,font-weight,font-style',
            // Only http/https/mailto links survive; javascript: is dropped.
            'URI.AllowedSchemes'        => ['http' => true, 'https' => true, 'mailto' => true],
            'Attr.AllowedFrameTargets'  => ['_blank'],
            // Prevent reverse-tabnabbing on target=_blank links.
            'HTML.TargetNoopener'       => true,
            'HTML.TargetNoreferrer'     => true,
            'AutoFormat.AutoParagraph'  => false,
            'AutoFormat.RemoveEmpty'    => true,
        ],
    ],
];
