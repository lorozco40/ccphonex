<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archivo extends MY_Controller
{

    public function __construct(){
        parent::__construct();
    }

    public function index() {
        ignore_user_abort(true);
        set_time_limit(0); // disable the time limit for this script

        if(isset($_GET['type']) && isset($_GET['name'])) {
            $paths = array('form'=>'upload/form/');
            $type = $_GET['type'];
            if ($type == 'form') {
                $parts = explode("_", $_GET['name']);
                $fullPath = FCPATH.'../'.$paths[$type].$parts[0].'/'.$parts[1].'/'.$parts[2].'/'.$_GET['name'];
            }

            if (file_exists($fullPath)) {
                $fd = fopen($fullPath, "r");
                $fsize = filesize($fullPath);
                $path_parts = pathinfo($fullPath);
                $ext = strtolower($path_parts["extension"]);
                switch ($ext) {
                    case "pdf":
                        header("Content-type: application/pdf");
                        header("Content-Disposition: filename=\"".$path_parts["basename"]."\""); // use 'Disposition: attachment; filename' to force a file download
                        break;
                    // add more headers for other content types here
                    default:
                        header("Content-type: application/octet-stream");
                        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
                        break;
                }
                header("Content-length: $fsize");
                header("Cache-control: private"); //use this to open files directly
                while(!feof($fd)) {
                    $buffer = fread($fd, 2048);
                    echo $buffer;
                }
                fclose ($fd);
            } else {
                echo "Archivo inexistente!";
            }
        } else {
            echo "Archivo inexistente!";
        }
        exit;
    }

}

?>
