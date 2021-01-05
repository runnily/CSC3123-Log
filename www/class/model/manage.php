<?php
/**
 * A model class for the RedBean object manage
 *
 * @author Adanna Obibuaku <b8025187@newcastle.ac.uk>
 * @copyright 2020 Adanna
 * @package Framework
 * @subpackage SystemModel
 */
    namespace Model;

    use \Support\Context;
    use \R;

    class Manage extends \RedBeanPHP\SimpleModel {

/**
 * This is used to get managed project with its assiocated number of users, number of notes and ids
 * The context id
 * @param Context    $context needs to take in context
 * @return  array    $returns an array
 */
        function getProjects(Context $context) 
        {
            $mng = $mng = R::find('manage', 'user_id = ?', [$context->user()->id]);
            $prj = [];
            foreach ($mng as $m) 
            {
                $p = R::load('project', $m->project_id);
                $note_id = R::getCol('SELECT id FROM note WHERE project_id = :pro',
                    [':pro' => $m->project_id]);
                try {
                    $uploads = R::find('upload', 'note_id IN ('.R::genSlots($note_id). ')', $note_id);
                } catch (\Exception $e)
                {
                    $uploads = [];
                }

                $prj[$p->pname] = array(
                    'id' => $m->project_id,
                    'summary' => $p->summary,
                    'users' => R::count('manage', 'project_id = ?', [$m->project_id]),
                    'notes' => R::count('note', 'project_id = ?', [$m->project_id]),
                    'uploads' => $uploads
                );

            }
            return $prj;
        }

    }
?>