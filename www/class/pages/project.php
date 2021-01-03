<?php
/**
 * A class that contains code to handle any requests for  /project/
 *
 * @author Adanna
 * @copyright 2020
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;
    use \Support\Context as Context;
    use \Framework\Local;
    use \R;

/**
 * A traits which has code that supports adding and deleting nodes
 * all methods used within the handle method
 */
trait notesAndUsers {

/**
 * This function is used to add notes
 */
    function addNote(Context $context, $prj) : void
    {
        $formp = $context->formdata('post');
        $title = $formp->fetch('title', '', FALSE);
        if ($formp->exists('title'))
        {
            if ( trim($title) !== '') 
            {
                $summary = $formp->fetch('summary', '', FALSE);
                $time = (int) $formp->fetch('time', '0', FALSE);
                $prj->addNote($context, TRUE, $title, $summary, $time, 'myfile', $context->rest()[0]);
                $context->local()->message(local::MESSAGE, "Note added!");
            } else 
            {
                $context->local()->message(local::WARNING, 'Please add a title');
            }
        }
    }

/**
 * This function is used to remove note
 */
    function removeNote(Context $context) : void
    {
        if ( count($context->rest()) == 4 && $context->rest()[2] == 'delete' && $context->rest()[1] == 'note') 
            {
            $trash = R::findOne('note', 'project_id = ? AND id = ?', [ $context->rest()[0], $context->rest()[3] ] );
            if ($trash) 
            {
                R::trash($trash);
                $context->local()->message(local::MESSAGE, "Note deleted");
            }
        }
    }

/**
 * This function is used to add a user
 */
    function addUser(Context $context, $prj) : void
    {
        $formuser = $context->formdata('post');
        $username = $formuser->fetch('username', '', FALSE);
        if ( strlen($username)>0 ) 
        {
            $bool = $formuser->fetch('admin', 0, FALSE) === 'TRUE' ?  TRUE : FALSE;
            try {
                $bool = $prj->addUser($username, $bool);
                if ($bool) 
                {
                    $context->local()->message(local::MESSAGE, "{$username} Added!");
                } else 
                {
                    $context->local()->message(local::WARNING, "{$username} already added!");
                }
            } catch (\Exception $e) 
            { 
                $context->local()->message(local::ERROR, "{$username} does not exits");
            }
        }
    }

/**
 * This function is used to remove a user
 */
    function removeUser(Context $context, $prj) :void
    {
        if ( count($context->rest()) == 4 && $context->rest()[2] == 'delete' && $context->rest()[1] == 'user' )
        {
            $user_id = $context->rest()[3];
            $project_id = $context->rest()[0];
            if ($prj->deleteUser($user_id,$project_id)) 
            {
                $context->local()->message(local::MESSAGE, "User deleted");
            } else {
                $context->local()->message(local::ERROR, "Ensure there is more than 1 admin");
            }
        }
    }
    
/**
 * This is used for deleting projects
 */
    function removePrj(Context $context, $prj) : void
    {
        try 
        {
            if ( count($context->rest()) == 2 && $context->rest()[1] == 'delete')
            {
                R::trash($prj);
                $context->divert("/");
                $context->local()->message(local::MESSAGE, "Project deleted");
            }
        } catch (\Exception $e) 
        {
            $context->local()->message(local::ERROR, "Something went wrong! Ensure you are an admin to perform this action or there is more than 1 user contributing to this project");
        }
    }
}

/**
 * Support /project/
 */
    class Project extends \Framework\Siteaction
    {
        use notesAndUsers;
        
/**
 * Handle project operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {
            $project = $context->rest()[0];
            $context->local()->addval("projectid", $project); 

            $prj = R::findOne('project', 'id = ?', [$project] );
            if ($prj) // if we have a project 
            { 
                // passing values to twig to show the project information
                $context->local()->addval('projectName', $prj->pname);
                $context->local()->addval('projectSummary', $prj->summary); 
                $context->local()->addval("exits", 1); 
                $notes = R::find('note', 'project_id = ?', [$project] );
                $context->local()->addval("deletePrj", "/project/{$project}/delete"); 

                if ($notes) // if we have a note
                {
                    // passing values to twig to show note information
                    $context->local()->addval("notes", $notes); 
                    $context->local()->addval("currentNote", "/project/{$project}/note"); 
                }
                
                $users = R::getAll('SELECT user.login, user.id, manage.admin FROM user, manage WHERE user.id = manage.u_id AND manage.project_id = :project_id',
                    [':project_id' => "{$project}"]
                ); // get all users names and ids assoicated with the project

                if ($users) // if we have users
                {
                    // pass the values if we have a list of users
                    $context->local()->addval("users", $users); 
                    $context->local()->addval("currentUser", "/project/{$project}/user");
                }
                $context->local()->addval('project_id', $project); 

                try {
                    // try performing these action
                    $this->addUser($context, $prj);
                    $this->removeNote($context);
                    $this->addNote($context, $prj);
                    $this->removeUser($context, $prj);
                    $this->removePrj($context, $prj);
                } catch (\Exception $e) 
                {
                    // else catch the exception and display a message
                    $context->local()->message(local::MESSAGE, "Something went wrong!");
                }
            }

            return '@content/project.twig';
        }
    }
?>