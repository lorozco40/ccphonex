<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class imapClient
{

    private $id_cta = 0;
    private $inbox = FALSE;
    private $path;
    private $ci;

    public function __construct() {
        $this->ci = &get_instance();
    }

    function revisaCuenta($data) {
        $this->id_cta = $data->id;
        $this->path = FCPATH . "emailfiles/" . $data->id . "/";
        $seguridad = (!empty($data->in_seguridad)) ? $data->in_seguridad.'/novalidate-cert' : 'notls';
        $hostName = '{'.$data->in_servidor.':'.$data->in_puerto.'/'.$data->tipo.'/'.$seguridad.'}INBOX';
        $lapas = encuentra($data->in_pass, $data->email);
        $this->inbox = imap_open($hostName, $data->in_user, $lapas) or die(file_put_contents(FCPATH . 'cron.log', $data->email.": ".imap_last_error()."\n", FILE_APPEND));
        return $this->EmailGetMany();
    }

    function EmailEmbeddedLinkReplace($html, $cid, $link) {
        // In $html locate src="cid:$cid" and replace with $link.
        $cid='cid:'.substr($cid, 1, strlen($cid)-2);
        $newHtml = str_replace($cid, $link, $html);
        return $newHtml;
    }

    function trimArray($values) {
        $trimmed=array();
        foreach ($values as $value) {
            $trimmed[]=trim($value);
        }
        return $trimmed;
    }

    function extractField($fieldName, $values) {
        $index=array_search($fieldName, $values);
        $id=($index === FALSE) ? "" : $values[$index+1];
        return $id ;
    }

    function extractValue($prefix, $values) {
        $result="";
        foreach ($values as $value) {
            if (0===strpos($value, $prefix)) {
                $result=substr($value, strlen($prefix)+2,-1);
                continue;
            }
        }
        return $result;
    }

    function extractMimeFileName($values) {
        $filename = $this->extractField("X-Attachment-Id:", $values);
        if (empty($filename)) {
            $filename = $this->extractValue("filename", $values);
        }
        if (empty($filename)) {
            $filename = $this->extractValue("name", $values);
        }
        if (empty($filename)) {
            $filename="unknown";
        }
        return $filename;
    }

    function fetchImageInfo($emailNumber, $partNo) {
        $mime=imap_fetchmime($this->inbox, $emailNumber, $partNo, (FT_PEEK));
        $mime = preg_split('/\s+/', $mime);
        $mime = $this->trimArray($mime);
        $id = $this->extractField("Content-ID:", $mime);
        $filename = $this->extractMimeFileName($mime);
        $info=array('id'=>$id, 'filename' => $filename);
        return $info;
    }
    // Based upon http://php.net/manual/en/function.imap-fetchstructure.php.
    function EmailGetPart($emailNumber, $part, $partNo, $result) {
        $parameter = array();
        $attachments = array();
        $plainText = '';
        $htmlText = '';
        // GET DATA
        $data = ($partNo) ? imap_fetchbody($this->inbox,$emailNumber,$partNo) : imap_body($this->inbox, $emailNumber);
        // Any part may be encoded, even plain text messages, so check everything.
        $encoding = $part->encoding ;
        $type=$part->type;
        if ($encoding==ENCQUOTEDPRINTABLE) {
            $data = quoted_printable_decode($data);
        } elseif ($encoding==ENCBASE64) {
            $data = base64_decode($data);
        }
        // PARAMETERS
        // get all parameters, like charset, filenames of attachments, etc.
        if($part->ifdparameters) {
            foreach($part->dparameters as $object) {
                $parameter[strtolower($object->attribute)] = $object->value;
            }
        }
        if($part->ifparameters) {
            foreach($part->parameters as $object) {
                $parameter[strtolower($object->attribute)] = $object->value;
            }
        }
        // ATTACHMENT
        // Any part with a filename is an attachment,
        // so an attached text file (type 0) is not mistaken as the message.
        if(isset($parameter['filename']) || isset($parameter['name'])) {
            $filename = (isset($parameter['filename'])) ? $parameter['filename'] : $parameter['name'];
            $filename = iconv_mime_decode($filename, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
            $id = isset($part->id) ? $part->id : '' ;
            $attachments[] = array('inline' => false, 'filename' => $filename, 'part' => $partNo, 'data' => $data, 'id' => $id);
        }
        if ($type==TYPEIMAGE) {
            $info = $this->fetchImageInfo($emailNumber, $partNo);
            $attachments[] = array('inline' => true, 'filename' => $info['filename'], 'part' => $partNo, 'data' => $data, 'id' => $info['id']);
        }
        if ((!empty($data)) && empty($filename)) {
            if ($type==TYPETEXT) {
                // Messages may be split in different parts because of inline attachments,
                // so append parts together with blank row.
                if (strtolower($part->subtype)=='plain') {
                    $plainText.= trim($data) ."\n\n";
                } else {
                    $htmlText.= $data ."<br><br>";
                }
                // assume all parts are same charset
                $result->charset = $parameter['charset'];
            } elseif ($type==TYPEMESSAGE) {
                // EMBEDDED MESSAGE
                // Many bounce notifications embed the original message as type 2,
                // but AOL uses type 1 (multipart), which is not handled here.
                // There are no PHP functions to parse embedded messages,
                // so this just appends the raw source to the main message.
                $plainText.= $data."\n\n";
            }
        }
        // SUBPART RECURSION
        $result->attachments = array_merge($result->attachments, $attachments);
        $result->plainText   = $result->plainText . $plainText;
        $result->htmlText    = $result->htmlText . $htmlText;
        if (isset($part->parts)) {
            $result = $this->EmailGetParts($emailNumber, $part->parts, $partNo, $result);
        }
        return $result;
    }

    function EmailGetParts($emailNumber, $parts, $partNo, $result) {
        if (isset($parts) && count($parts)) {
            foreach ($parts as $partIx=>$subPart) {
                $subPartNo = empty($partNo) ? ($partIx+1) : $partNo . '.' . ($partIx+1);
                $result = $this->EmailGetPart($emailNumber, $subPart, $subPartNo, $result);
            }
        }
        return $result ;
    }

    function DecodeMailHeader($headerInfo, $fieldName) {
        $value='';
        if (isset($headerInfo->$fieldName)) {
            $value = iconv_mime_decode($headerInfo->$fieldName, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');
        }
        return $value ;
    }

    function EmailGetOne($email_number) {
        $structure = imap_fetchstructure($this->inbox, $email_number);
        $mail = new stdClass();
        $mail->attachments = array();
        $mail->plainText   = '';
        $mail->htmlText    = '';
        $mail->charset     = 'auto';
        $mail->id_cta      = $this->id_cta;
        if (empty($structure->parts)) {
            // Simple message.
            $mail = $this->EmailGetPart($email_number, $structure, 0, $mail);
        } else {
            // Multipart message.
            $mail = $this->EmailGetParts($email_number, $structure->parts, 0, $mail);
        }
        $headerInfo = imap_headerinfo($this->inbox, $email_number, 0);
        $headerInfo->reply_toaddress = $this->DecodeMailHeader($headerInfo, 'reply_toaddress');
        $headerInfo->senderaddress = $this->DecodeMailHeader($headerInfo, 'senderaddress');
        $headerInfo->fromaddress = $this->DecodeMailHeader($headerInfo, 'fromaddress');
        $headerInfo->toaddress = $this->DecodeMailHeader($headerInfo, 'toaddress');
        $headerInfo->ccaddress = $this->DecodeMailHeader($headerInfo, 'ccaddress');
        $headerInfo->subject = $this->DecodeMailHeader($headerInfo, 'subject');
        $headerInfo->Subject = $this->DecodeMailHeader($headerInfo, 'Subject');
        ini_set('mbstring.substitute_character', 'none'); //32 to substitute a space, "none" to remove
        $headerInfo->reply_to[0]->personal = (!empty($headerInfo->reply_to[0]->personal)) ? str_replace("_"," ", mb_decode_mimeheader($headerInfo->reply_to[0]->personal)) : '';
        $headerInfo->sender[0]->personal = (!empty($headerInfo->sender[0]->personal)) ? str_replace("_"," ", mb_decode_mimeheader($headerInfo->sender[0]->personal)) : '';
        $headerInfo->from[0]->personal = (!empty($headerInfo->from[0]->personal)) ? str_replace("_"," ", mb_decode_mimeheader($headerInfo->from[0]->personal)) : '';
        $mail->headerInfo = $headerInfo;
        $mail->plainText = mb_convert_encoding($mail->plainText, 'UTF-8', $mail->charset);
        $mail->htmlText  = mb_convert_encoding($mail->htmlText, 'UTF-8', $mail->charset);
        if (empty($mail->htmlText)) {
            $mail->htmlText='<p>'.$mail->plainText.'</p>';
        }
        $this->EmailAttachmentsSave($mail);
        return $mail;
    }

    function EmailGetMany() {
        if (!empty($this->inbox)) {
            $mails = array();
            $emails = imap_search($this->inbox, 'UNSEEN');
            if($emails) {
                /* put the newest emails on top, uncoment next line */
                // rsort($emails);
                foreach($emails as $email_number) {
                    $mails[]=$this->EmailGetOne($email_number);
                    echo imap_setflag_full($this->inbox, $email_number, "\\Deleted"); // para imap \\Seen \\Flagged
                }
                imap_expunge($this->inbox);
            }
            imap_close($this->inbox);
            return $mails;
        } else {
            return $this->inbox;
        }
    }

    function EmailAttachmentsSave(&$mail) {
        $html = '';
        $msgNo = trim($mail->headerInfo->Msgno);
        $attstexto = array();

        foreach ($mail->attachments as $attachment) {
            if (!empty($attachment)) {
                $partNo   = $attachment['part'];
                $fileName = $attachment['filename'];
                $absDir   = $this->path . $msgNo ."/". $partNo;
                if (!is_dir($absDir)) {
                    mkdir($absDir, 0777, true);
                    shell_exec("chown -R asterisk.asterisk " . FCPATH . "emailfiles");
                }
                $absFileName = $absDir."/".$fileName;
                if (file_exists($absFileName)) {
                    $fileName = time().$fileName;
                    $absFileName = $absDir."/".$fileName;
                }
                $parFileName = $msgNo."/".$partNo."/".$fileName;
                file_put_contents($absFileName, $attachment['data']);
                if (!in_array($parFileName, $attstexto)) $attstexto[] = $parFileName;
                $cid = $attachment['id'];
                if (isset($cid)) {
                    $htmlFileName = $this->id_cta."/".$parFileName;
                    $htmlFileName = htmlentities($htmlFileName);
                    $mail->htmlText = $this->EmailEmbeddedLinkReplace($mail->htmlText,$cid,$htmlFileName);
                }
            }
        }
        $mail->attstexto = implode($attstexto, ",");
        return $html;
    }

}

?>
