<?php
/**
 * A class that contains code to handle any requests for  /create/
 *
 * @author Adanna Obibuaku <b8025187@newcastle.ac.uk>
 * @copyright 2020 Adanna
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \Framework\Local;
    use \R;

/**
 * Support /create/
 */
    class Create extends \Framework\Siteaction
    {
/**
 * Handle create operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $formd = $context->formdata('post'); // get name

            // if we have a title for the project
            if ($formd->exists('pname')) 
            {

                $name = $formd->fetch('pname', '', FALSE);

                // test name is valid
                if (isset($name) || trim($name) !== '')
                {
                    $prj = R::dispense('project');
                    $dup = R::getAll('SELECT project.pname FROM project, user, manage WHERE project.id = manage.project_id AND user.id = manage.u_id AND project.pname = :pname',
                        [':pname' => "{$name}"]
                    ); // check if there is already a duplicate
                    
                    // when there is no duplicates
                    if (!$dup) 
                    { 
                        $prj->pname = $name;
                        $prj->summary = $formd->fetch('summary', '', FALSE);

                        $mng = R::dispense('manage');
                        // manages has a list of users managing many list of projects
                        $mng->noLoad()->u = $context->user();
                        $prj->noLoad()->xownManage[]= $mng;

                        $mng->admin = TRUE;
                        R::store($mng);
                        R::store($prj);
                        $context->divert("/");
                        $context->local()->message(Local::MESSAGE, "{$name} Project created!");
                    } else 
                    {
                        $context->local()->message(Local::ERROR, "{$name} already exits!");
                    }

                } else 
                {
                    $context->local()->message(Local::ERROR, "Please insert a name!");
                }
            }

            return '@content/create.twig';
        }
    }
?>