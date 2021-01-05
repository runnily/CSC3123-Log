<?php
 /**
  * Class for handling home pages
  *
  * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
  * @copyright 2012-2019 Newcastle University
  * @package Framework
  * @subpackage UserPages
  */
    namespace Pages;
    use \Framework\Local;
    use \R;

    use \Support\Context;
/**
 * A class that contains code to implement a home page
 * @psalm-suppress UnusedClass
 */
    class Home extends \Framework\SiteAction
    {
/**
 * Handle various contact operations /
 *
 * @param Context   $context    The context object for the site
 *
 * @return string   A template name
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
        public function handle(Context $context)
        {
            //R::exec('SET FOREIGN_KEY_CHECKS=0; DROP TABLE project; SET FOREIGN_KEY_CHECKS=1;'); 
            if ($context->hasUser())
            {   
                $prj = [];
                
                $mng = $context->user()->ownManage;
                $userNotesTotal = 0;
                foreach ($mng as $m) 
                {
                    $m = $m->fresh();
                    $p = R::load('project', $m->project_id);
                    $prj[$p->pname] = $p->contributions();
                    $userNotesTotal += $p->contributions();
                }

            $context->local()->addval('total', $userNotesTotal);
            $context->local()->addval('projects1', $prj);

            $mng = R::dispense('manage');
            $context->local()->addval('projects2', $mng->getProjects($context) );
            }   
            return '@content/index.twig';
        }
    }
?>
