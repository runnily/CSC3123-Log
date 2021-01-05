<?php
/**
 * Class to handle the FrameworkFramework AJAX SAMPLE operation
 *
 * @author Adanna Obibuaku
 * @copyright 2020 Adanna
 * @package Framework
 * @subpackage UserAjax
 */
    namespace Ajax;
    use \Framework\Local;

/**
 * This is used to get update project name and title
 */
    class InsertProject extends \Framework\Ajax\Ajax
    {
/**
 * @var array If you want to use the permission checking functions provided by the Framework then you will need
 *            an array of values defining them. The format is:
 *            [
 *                  'beanname'  => [ TRUE/FALSE (login required), [['ContextName', 'RoleName']...], [..field names or empty for all...],
 *                  ....
 *            ]
 *            For an example of how to use this array, see the implementation of \Framework\Ajax\Bean in class/framework/ajax/bean.php
 *            The checking functions are defined in the base class \Framework\Ajax\Ajax in class/framework/ajax/ajax.php
 *
 *            If you just want to control access to this operation then just put the list of
 *            contextname/rolename pairs in the result of the requires method below an leave this empty.
 *
 */
  
/**
 * Return permission requirements. The version in the base class requires logins and adds nothing else.
 * If that is what you need then you can remove this method. This function is called from the base
 * class constructor when it does some permission checking.
 *
 * @return array
 */
        public function requires()
        {
            return [TRUE, []]; // requires login
        }
/**
 * This is used to update the project name and summary with the user typing
 *
 * @return void
 */
        final public function handle() : void
        {
            $context = $this->context;
            $rest = $context->rest();
            $prj = \R::load('project', $rest[1]);
            $update = $rest[3];

            if ($prj && strlen($update) > 0 && trim($update) != '')
            {
                if ($rest[2] == 'title')
                {
                    $dup = \R::getAll('SELECT project.pname FROM project, user, manage WHERE project.id = manage.project_id AND user.id = manage.u_id AND project.pname = :pname',
                        [':pname' => "{$update}"] // checking for duplicates
                    ); 
                    if (!$dup) 
                    {
                        $prj->pname = $update;
                        \R::store($prj);
                    }
                } 
                if ($rest[2] == 'summary')
                {
                    $prj->summary = $update;
                    \R::store($prj);
                }
            }
        }
    }
?>