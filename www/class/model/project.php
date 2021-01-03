<?php
/**
 * A model class for the RedBean object project
 *
 * @author Adanna Obibuaku <b8025187@newcastle.ac.uk>
 * @copyright 2020 Adanna
 * @package Framework
 * @subpackage SystemModel
 */
    namespace Model;

    use \Support\Context;
    use \R;

    class Project extends \RedBeanPHP\SimpleModel 
    {
/**
 * This is used for adding notes to a project
 * @param Context   $context    The context of the object
 * @param bool      $new        If this is a new node a node being updated
 * @param string    $title      The title of the note
 * @param string    $summary    The summary of the note
 * @param int       $time       This tell us the time
 * @param string    $myfile     The name of the input file
 * @param string    $projectid  The name of the project id
 */

        function addNote(Context $context, bool $new = FALSE, string $title = '', string $summary = '', int $time = 0, string $myfile = '', string $projectid = '') : void
        {
            $note = R::dispense('note');
            $note->addEdit($context, $new, $title , $summary, $time, $myfile , $projectid );
        }
/**
 * This function is used to add users
 * @param string    $username       is the name of the user to add
 * @param bool      $admin          true if user is admin, else false if user is not admin
 * @return bool                     Will return true if its has successfully added the user
 * @throws Exception                If the user does not exits.
 */
        function addUser(string $username, bool $admin = FALSE) : bool 
        {
            $prs = R::findOne('user', 'login = ?', [$username] ); // finds user exits in database
            if ($prs) 
            { // checking if user exits
                $dup =  R::getAll('SELECT user.login FROM user, manage WHERE user.id = manage.u_id AND manage.project_id = :project_id AND user.login = :user_login',
                [':project_id' => "{$this->id}", ':user_login' => "{$username}"]
                ); 
                if (!$dup) 
                { // if user is not already managing project
                    $mng = R::dispense('manage');
                    $mng->u_id = $prs->id;
                    $mng->admin = $admin;
                    $mng->project = $this;
                    R::store($mng);
                    return true;
                } 
            } else 
            {
                throw new \Framework\Exception\Forbidden('User does not exits');
            }
            return false; 
        }

/**
 *  This is used for removing users
 *  @param string       $user_id        This takes in the user id
 *  @param string       $project_id     This takes in the project id
 *  @return  bool       Will return true when user is successfully deleted else false
 */
        function deleteUser($user_id, $project_id = '') : bool
        {
            // ensures there is more than 1 user and they are admin
            // admin can only delete user
            if (R::count('manage', 'project_id = ?', [$this->bean->id]) > 1 && $this->isAdmin(Context::getinstance()))
            {
                $numAdmins = R::count('manage', 'admin = ? AND project_id = ?', [TRUE, $this->bean->id] );
                $trash = R::findOne('manage', 'project_id = ? AND u_id = ?', [ $project_id, $user_id]);

                if ($trash) { // if user exists (is managing this current project)
                    
                    // check if thier admin and number of admins is greater than one
                    if ($trash->admin && $numAdmins > 1)  
                    {
                        R::trash($trash);
                        return TRUE;

                    } else  // if not
                    {
                        if (!$trash->admin) // check if user is NOT an admin
                        {
                            R::trash($trash); // delete them
                            return TRUE;
                        }
                    }
                }     
            }
            return FALSE;
        }
/**
 * Will return if the user is admin or not
 * @param context   $context    Denotes the context
 * @return bool                 Indicates is user is admin or not
 */
        function isAdmin(Context $context) : bool 
        {
            if (R::count('manage', 'project_id = ?', [$this->bean->id]) <= 0)
            {
                return TRUE; // If no one is managing it then it can be changed by anyone
            }
            $mng = R::findOne('manage', 'project_id = ? AND u_id = ?', [$this->bean->id, $context->user()->id]);
            return $mng->admin;
        }

/**
 * This caculates the contributes of a project in terms of the users and logs
 * @return int  Provides number of users and logs
 */
    function contributions() : float
    {   
        $INTOHOURS = 60;
        $query = 'project_id = ?';
        $mng = R::count('manage', $query ,[$this->bean->id]);
        $notes = R::count('note', $query, [$this->bean->id]);
        if ($notes != 0) {
            $hours = R::findOne('note', $query ,[$this->bean->id])->minutes/$INTOHOURS;
            return $mng + $notes + $hours;
        }
        return $mng + $notes;
    }

/**
 * This would take care of deleting the bean
 * @return void
 */
        function delete() : void
        {
            $context = Context::getinstance();
            $numAdmins = R::count('manage', 'admin = ? AND project_id = ?', [TRUE, $this->bean->id] );

            if (!($this->bean->isAdmin($context)) && $numAdmins > 0) 
            {
                throw new \Framework\Exception\Forbidden('User is not admin!');
            }
        
            $trash = R::find('note', 'project_id = ?', [$this->bean->id]);
            if ($trash) 
            {
                R::trashAll($trash);
            }
            
        } 
    }
?>