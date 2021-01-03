<?php
/**
 * A class that contains code to handle any requests for  /profile/
 *
 * @author Adanna Obibuaku <b8025187@newcastle.ac.uk>
 * @copyright 2020 Adanna
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
/**
 * Support /profile/
 */
    class Profile extends \Framework\Siteaction
    {
/**
 * Handle profile operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            if ($context->hasUser()) 
            {   
                $context->local()->addval('login', $context->user()->login );
                $context->local()->addval('email', $context->user()->email );
            }
            return '@content/profile.twig';
        }
    }
?>