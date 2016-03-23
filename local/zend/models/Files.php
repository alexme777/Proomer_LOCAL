<?
/**
 * Class Sibirix_Model_Files
 */
class Sibirix_Model_Files {

    /* @var CFile */
    protected $_bxFile;
    public $lastError;

    public function __construct() {
        $this->_bxFile = new CFile();
    }

    /**
     * @param $fileArray
     * @return int|string
     */
    public function saveFile($fileArray) {
        return $this->_bxFile->SaveFile($fileArray, "tmp_files");
    }

    /**
     * @param $fileId
     */
    public function deleteFile($fileId) {
        if(is_array($fileId)) {
            foreach ($fileId as $id) {
                $this->_bxFile->Delete($id);
            }
        } else {
            $this->_bxFile->Delete($fileId);
        }
    }

    /**
     * @param $fileId
     * @return bool|int|mixed|string
     */
    public function copyFile($fileId){
        return $this->_bxFile->CopyFile($fileId);
    }


    /**
     * Проверяет файл на расщирения
     * @param $file
     * @param $extensions
     * @return bool
     */
    public function checkFile($file, $extensions) {
        $res = $this->_bxFile->CheckFile($file, 0, false, $extensions);
        return !(strlen($res)>0);
    }
}
