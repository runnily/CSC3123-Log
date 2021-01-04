<?php
/**
 * A model class for the RedBean object note
 *
 * @author Adanna Obibuaku <b8025187@newcastle.ac.uk>
 * @copyright 2020 Adanna
 * @package Framework
 * @subpackage SystemModel
 */
    namespace Model;

    use \Support\Context;
    use \R;

    class Note extends \RedBeanPHP\SimpleModel {

/**
 * This is used for creating or updating a note.
 * 
 * @param Context   $context    The context of the object
 * @param bool      $new        If this is a new node a node being updated
 * @param string    $title      The title of the note
 * @param string    $summary    The summary of the note
 * @param int       $time       This will provide the minutes
 * @param string    $myfile     The name of the input file
 * @param string    $projectid  The name of the project id
 * @return bool                 This indicated whether the note has been updated/added
 */
        function addEdit(Context $context, bool $new = FALSE, string $title = '', string $summary = '', int $time = 0, string $myfile = '', string $projectid = '') : bool
        {
            $added = FALSE;

            // Sets title if there is one
             if (strlen($title) > 0) 
            {
                $this->bean->title = $title;
                $added = TRUE;
            }
            // Sets title if there is one
            if (strlen($summary) > 0) 
            {
                $this->bean->summary = $summary;
                $added = TRUE;
            }
            
            if ($time > 0) {
                $this->bean->minutes += $time;
                $added = TRUE;
            }

            // Sets file if there is one
            $fdf = $context->formdata('file'); // if the user updates file
            if ($fdf->exists($myfile)) 
            { 
                $fa = $fdf->fileData('myfile');
                if ($fa['name']) 
                {
                    $upl = R::dispense('upload');
                    $upl->savefile($context, $fa, TRUE, $context->user(), 0);
                    R::store($upl);
                    $this->bean->noLoad()->xownUploads[] = $upl;
                    $added = TRUE;
                }
            }
            $log = R::dispense('log');
            $log->update = R::isoDateTime();
            $log->num += 1;
            R::store($log);

            // if its a new project
            if ($new) 
            {
                $prj = R::load('project', (int) $projectid);
                $prj->noLoad()->xownNote[] = $this->bean;
                $this->bean->time = R::isoDateTime();
                R::store($prj);
            }

            $this->bean->noLoad()->xownLogs[] = $log; // cascade so when I delete note log will also be deleted
            R::store($this);
            return $added;
        }

/**
 * This is to ensure when a node is directly calls the delete method in uploads to delete
 * all the files assoicated with it, on the directory.
 * @return void
 */
        function delete() : void
        {
            $trash = $this->bean->ownUpload;
            if ($trash) {
                R::trashAll($trash);
            }
        }
    }
?>