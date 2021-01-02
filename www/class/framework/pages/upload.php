<?php
/**
 * A class that contains code to handle any /upload related requests.
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @copyright 2015-2020 Newcastle University
 * @package Framework
 * @subpackage SystemPages
 */
    namespace Framework\Pages;

    use \Config\Config;
    use \Support\Context;
/**
 * Deal with a file upload URL
 */
    class Upload extends \Framework\SiteAction
    {
/**
 * Handle various admin operations /upload
 *
 * @param Context  $context    The context object for the site
 *
 * @return string   A template name
 */
        public function handle(Context $context)
        {
            $fdt = $context->formdata('file');
            if ($fdt->exists('uploads'))
            {
                if (Config::UPUBLIC && Config::UPRIVATE)
                { // need to check the flag could be either private or public
                    foreach ($fdt->fetchArray('public') as $ix => $public)
                    {
                        $upl = \R::dispense('upload');
                        $upl->savefile($context, $fdt->fileData('uploads', $ix), $public, $context->user(), $ix);
                    }
                }
                else
                {
                    foreach($fdt->fileArray('uploads') as $ix => $fa)
                    { // we only support private or public in this case so there is no flag
                        $upl = \R::dispense('upload');
                        $upl->savefile($context, $fa, Config::UPUBLIC, $context->user(), $ix);
                    }
                }
            }
            return '@content/upload.twig';
        }
    }
?>