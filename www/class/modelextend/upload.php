<?php
/**
 * A trait that allows extending the model class for the RedBean object Upload
 *
 * Add any new methods you want the Upload bean to have here.
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @copyright 2018-2020 Newcastle University
 * @copyright 2018-2020 Adanna Obibuaku
 * @package Framework
 * @subpackage ModelExtend
 */
    namespace ModelExtend;

    use \Support\Context;
/**
 * Upload table stores info about files that have been uploaded...
 */
    trait Upload
    {
/**
 * Determine if a user can access the file
 *
 * At the moment it is either the user or any admin that is allowed. Rewrite the
 * method to add more complex access control schemes.
 *
 * @param object   $user   A user object
 * @param string   $op     r for read, u for update, d for delete
 *
 * @return bool
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
        public function canaccess($user, string $op = 'r') : bool
        {
            try 
            {
                return $this->bean->user->equals($user) || $user->isadmin();
            } catch (\Throwable $t ) 
            {
                return TRUE; // if the is not user, or user admin assoicated to object
            }
        }
/**
 * Hook for adding extra data to a file save.
 *
 * @param Context   $context    The context object for the site
 * @param int       $index      If you are reading data from an array fo files, this is the index
 *                              in the file. You may have paralleld data arrays and need this index.
 *
 * @return void
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
        public function addData(Context $context, int $index) : void
        {
            /*
             * Your code goes here
             */
        }
/**
 * Hook for adding extra data to a file replace.
 *
 * @param Context    $context   The context object for the site
 * @param int        $index     If you are reading data from an array of files, this is the index
 *                              in the file. You may have parallel data arrays and need this index.
 *
 * @return void
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
        public function updateData(Context $context, int $index = 0) : void
        {
            /*
             * Your code goes here
             */
        }
/**
 * Hook for doing something when a file is downloaded
 *
 * @param Context    $context   The context object for the site
 *
 * @return void
 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
 */
        public function downloaded(Context $context) : void
        {
            $fname = $context->local()->basedir().$this->bean->fname;
            $type = explode('.' , $this->bean->filename); // get the type of file
            $type = $type[count($type)-1];
            
            switch ($type) {
                case "pdf": $ctype="application/pdf"; break;
                case "exe": $ctype="application/octet-stream"; break;
                case "zip": $ctype="application/zip"; break;
                case "doc": $ctype="application/msword"; break;
                case "xls": $ctype="application/vnd.ms-excel"; break;
                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpe": case "jpeg":
                case "jpg": $ctype="image/jpg"; break;
                default: $ctype="application/force-download";
            }

            if (!file_exists($fname)) {
                throw new \Framework\Exception\Forbidden('NO SUCH FILE');
            }

            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false);
            header("Content-Type: $ctype");
            //sets filename
            header("Content-Disposition: attachment; filename=\"".basename($this->bean->filename)."\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".filesize($fname)); // reads size
            readfile($fname);  // read the file
        }
/**
 * Automatically called by RedBean when you try to trash an upload. Do any cleanup in here
 *
 * @param Context $context
 *
 * @throws \Framework\Exception\Forbidden
 * @return void
 */
        public function delete() : void
        {
/* **** Do not change this code **** */
            $context = Context::getinstance();
            if (!$this->bean->canaccess($context->user(), 'd'))
            { // not allowed
                throw new \Framework\Exception\Forbidden('Permission Denied');
            }
// Now delete the associated file
            $fname = $context->local()->basedir().$this->bean->fname;
            if (is_file($fname) && file_exists($fname)) {
                unlink($context->local()->basedir().$this->bean->fname);
            }
/* **** Put any cleanup code of yours after this line **** */
            /*
             * Your code goes here
             */
        }
    }
?>
