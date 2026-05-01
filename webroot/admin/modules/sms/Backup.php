<?php
namespace FreePBX\modules\Sms;
use FreePBX\modules\Backup as Base;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
class Backup Extends Base\BackupBase{
	public function runBackup($id,$transaction){
		global $amp_conf;
		//dump the sms tables to sms_dump.sql file
		$host = $amp_conf['AMPDBHOST'];
		$port = isset($amp_conf['AMPDBPORT'])?$amp_conf['AMPDBPORT']:'';
		$dbuser = $this->FreePBX->Config->get('AMPDBUSER')?$this->FreePBX->Config->get('AMPDBUSER'):$amp_conf['AMPDBUSER'];
		$dbpass = $this->FreePBX->Config->get('AMPDBPASS')?$this->FreePBX->Config->get('AMPDBPASS'):$amp_conf['AMPDBPASS'];
		$dbname = $this->FreePBX->Config->get('AMPDBNAME') ? $this->FreePBX->Config->get('AMPDBNAME') : 'asterisk';
		$fs = new Filesystem();
		$tmpdir = sys_get_temp_dir().'/smsdump';
		$fs->remove($tmpdir);
		$fs->mkdir($tmpdir);
		$tmpfile = $tmpdir."/sms_dump.sql";
		$tables = $this->getTablenames();
		$smstables = implode(' ', $tables);
		$mysqldump = fpbx_which('mysqldump');
		if($host =='localhost' || $host == '127.0.0.1'){
			$hostname = '';
		}else {
        		$hostname = '-h '.$host;
		}
		if($port ==''){
			$portnum = '';
		}else {
			$portnum = '-P '.$port;
		}
		$command = "{$mysqldump} {$portnum} {$hostname} -u{$dbuser} -p{$dbpass} {$dbname} {$smstables} --result-file={$tmpfile}";
		$process= new Process($command);
		$process->disableOutput();
		$process->mustRun();
		$fileObj = new \SplFileInfo($tmpfile);
		$this->addSplFile($fileObj);
		$this->addDependency('ucp');
	}

	private function getTablenames() {
		$module = strtolower($this->data['module']);
		$this->log(sprintf(_("Exporting Databases from %s"), $module));
		$dir = $this->FreePBX->Config->get('AMPWEBROOT').'/admin/modules/'.$module;
		if(!file_exists($dir.'/module.xml')) {
			return [];
		}
		$xml = simplexml_load_file($dir.'/module.xml');
		$tables = [];
		if(is_object($xml->database->table)) {
			foreach($xml->database->table as $table) {
				$tname = (string)$table->attributes()->name;
			$tables[$tname] = $tname;
			}
		}
		return $tables;
	}
}
