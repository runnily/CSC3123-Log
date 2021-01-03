<?php
/**
 * A class that contains code to handle any requests for  /note/
 *
 * @author Your Name <Your@email.org>
 * @copyright year You
 * @package Framework
 * @subpackage UserPages
 */
    namespace Pages;

    use \Support\Context as Context;
    use \Framework\Local;
    use \R;


/**
 * Support /note/
 */
    class Note extends \Framework\Siteaction
    {
/**
 * Handle note operations
 *
 * @param Context   $context    The context object for the site
 *
 * @return string|array   A template name
 */
        public function handle(Context $context)
        {   
            $rest = $context->rest();
            $pid = $rest[2];
            $nid = $rest[0];

            $note = R::findOne('note', 'project_id = ? AND id = ?', [$pid, $nid]);
            $context->local()->addval('note', $note); 

            $uploads = R::find('upload', 'note_id = ?', [$nid]);
            $context->local()->addval('uploads', $uploads); 
            $context->local()->addval('noteid', $nid);
            $context->local()->addval('projectid', $pid);

            $context->local()->addval("download", "/note/{$nid}/project/{$pid}/download");
            $context->local()->addval("delete", "/note/{$nid}/project/{$pid}/delete");
            
            // This updates the note
            $fdp = $context->formdata('post');
            $note = R::load('note', $nid);
            try {
                
                if ($note->addEdit($context, FALSE, $fdp->fetch('title', '', FALSE), $fdp->fetch('summary', '', FALSE), (int) $fdp->fetch('time', '0', FALSE), 'myfile'))
                {
                    $context->local()->message(Local::MESSAGE, 'Note Updated!' );
                }
            } catch (\Exception $e)
            {
                $context->local()->message(Local::ERROR, 'Something went wrong!' );
            }   
            R::store($note);
            
            if (count($rest) == 5)
            {
                $uid = $rest[4];
                $upl = R::load("upload", $uid);
                if ($rest[3] == 'download') 
                {
                    $upl->downloaded($context);
                }
                if ($rest[3] == 'delete') 
                {
                    R::trash($upl);
                    $context->local()->message(Local::MESSAGE, 'File Deleted!' );
                }
            }
            return '@content/note.twig';
        }
    }
?>