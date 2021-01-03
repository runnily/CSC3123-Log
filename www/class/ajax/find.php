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

/**
 * This is to find a specfic type of bean, or use to find a list of beans 
 */
    class Find extends \Framework\Ajax\Ajax
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
            return [FALSE, [/* [contextname/rolename],... */]]; // does not require login, no contextname/rolename checks
        }
/**
 * Carry out SAMPLE operation
 *
 * @return void
 */
        final public function handle() : void
        {
            $context = $this->context;
            $id = $context->rest()[2];
            $type = $context->rest()[1];
            if ($type == 'upload') 
            {
                $beans = \R::find($type, 'note_id = ?', [$id]);

            } else if ($type == 'note')
            {
                $beans = \R::find($type, 'project_id = ?', [$id]);

            } else if ($type == 'user') 
            {
                $beans = \R::getAll('SELECT user.login, user.id, manage.admin FROM user, manage WHERE user.id = manage.u_id AND manage.project_id = :project_id',
                    [':project_id' => "{$id}"]
                );

            } else 
            {
                $beans = \R::load($type, $id);

            }
            $context->web()->sendJSON($beans);
        }
    }
?>