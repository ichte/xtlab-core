<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 07-Sep-18
 * Time: 12:23 AM
 */

namespace XT\Core\Validator;


use Zend\Validator\File\Extension;

class ExtensionAllow extends Extension
{
    /**
     * Returns true if and only if the file extension of $value is included in the
     * set extension list
     *
     * @param  string|array $value Real file to check for extension
     * @return bool
     */
    public function isValid($value)
    {
        $file     = $value;
        $filename = basename($file);

        $this->setValue($filename);

        // Is file readable ?
        if (empty($file)) {
            $this->error(self::FALSE_EXTENSION);
            return false;
        }

        $extension  = substr($filename, strrpos($filename, '.') + 1);
        $extensions = $this->getExtension();

        if ($this->getCase() && (in_array($extension, $extensions))) {
            return true;
        } elseif (! $this->getCase()) {
            foreach ($extensions as $ext) {
                if (strtolower($ext) == strtolower($extension)) {
                    return true;
                }
            }
        }

        $this->error(self::FALSE_EXTENSION);
        return false;
    }
}