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
 * This is used to get the notes
 */
    class FindNote extends \Framework\Ajax\Ajax
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
            $project = $context->rest()[1];
            $notes = \R::find('note', 'project_id = ?', [$project]);
            $context->web()->sendJSON($notes);
        }
    }
?>