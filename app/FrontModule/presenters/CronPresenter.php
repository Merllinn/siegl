<?php

namespace App\FrontModule\Presenters;

use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Utils\Finder;


class CronPresenter extends BasePresenter
{
	public function startup(){
		parent::startup();
	}

	public function actionDefault(){
    	set_time_limit(-1);
    	$this->removeZips();
    	$this->checkFolders();
    	$this->checkLastToGenerate();

        $this->terminate();
	}
	
	public function checkLastToGenerate(){
        $toGenerate = $this->projectManager->getToRefresh();
        foreach($toGenerate as $project){
        	$this->getTiffCount($this->rowToArray($project));
        }
        
        $toGenerate = $this->projectManager->getLatestToGenerate();
        if($toGenerate){
        	$this->generateJpegs($this->rowToArray($toGenerate));
        }
	}


	public function getTiffCount($project){
        $files = $this->getGfiles($project->hash);
		$tifs = array();
		foreach($files as $file){
			$ext = pathinfo($file->getName(), PATHINFO_EXTENSION);
			$name = pathinfo($file->getName(), PATHINFO_FILENAME);
			switch(strtolower($ext)){
				case "tif":
				case "tiff":
					$tifs[$name] = $file;
					break;
				default:
					break;
			}
		}
        $this->projectManager->update(array("toGenerate"=>count($tifs)), $project->id);
	}
	public function generateJpegs($project){
        $files = $this->getGfiles($project->hash);
		$jpegs = array();
		$tifs = array();
		foreach($files as $file){
			$ext = pathinfo($file->getName(), PATHINFO_EXTENSION);
			$name = pathinfo($file->getName(), PATHINFO_FILENAME);
			switch(strtolower($ext)){
				case "tif":
				case "tiff":
					$tifs[$name] = $file;
					break;
				case "jpg":
				case "jpeg":
					$jpegs[$name] = $file;
					break;
			}
		}
        $this->projectManager->update(array("toGenerate"=>count($tifs)), $project->id);

		$converted = count($jpegs);
        if(count($tifs)>0 && $converted>=count($tifs)){
        	$this->projectManager->update(array("generateStatus"=>20, "genError"=>""), $project->id);
        	echo("Dokončena zakázka ".$project->id."<br>");
    		$this->checkLastToGenerate();
        }else{
	        foreach($tifs as $fileName=>$fileObj){
				if(empty($jpegs[$fileName])){
					$file = $this->getGfile($fileObj->getId());
					$tiffName = $file->getName();
					$jpegName = $this->tiff2jpgName($tiffName);
					$tiffPath = DATA_DIR.'/temp/'.$tiffName;
        			$ft = fopen($tiffPath, 'w');
					//get data of original file
				    fwrite($ft, $this->getGfileMedia($fileObj->getId()));
				    //fwrite($ft, file_get_contents($file->getWebContentLink()));
				    fclose($ft);

					if(filesize($tiffPath)>0){
						//convert tiff to jpeg
						$jpegPath = $this->tiff2jpg($tiffPath, $project->id);

						//upload file to server
						if(!empty($jpegPath)){
							$converted++;
							$this->uploadGfile($jpegName, $jpegPath, $project->hash);
						}
						else{
        					$this->projectManager->update(array("generateStatus"=>90), $project->id);				
        				}
					}
					else{
        				$this->projectManager->update(array("generateStatus"=>90, "genError"=>"Soubor ".$file->getWebContentLink()." nebylo možné stáhnout."), $project->id);				
					}
				}

			}
        	echo("Vygenerována zakázka ".$project->id."<br>");
        }
        $this->projectManager->update(array("generated"=>$converted), $project->id);
	}
	
	public function actionAdmins(){
        $branches = $this->branchManager->getSimple();
		$day = new \Nette\Utils\DateTime();
		$day->modify("+1 day");
        foreach($branches as $branch){
			$orders = $this->prepareOrders($day, $branch);
			if(count($orders)>0){
				$admins = $this->userManager->findByBranch($branch);
				foreach($admins as $admin){
					$this->sendNotif($admin->email, $orders, $branch, $day);
				}
			}

        }

        $this->terminate();
	}

    public function prepareOrders($date, $branch){
        $orders = $this->orderManager->getAll();
        $orders->where("branch = ?", $branch);
        $orders->where("`from` = ?", $date->format("Y-m-d"));

        return $orders;
    }


    public function sendNotif($email, $orders, $branch, $day){
	    $template = $this->createTemplate();
	    $template->setFile(APP_DIR . '/FrontModule/templates/Mails/dailyNotif.latte');
	    $template->orders = $orders;
	    $template->branch = $branch;
	    $template->day = $day;

	    //send mail
	    $mail = new Message;
	    $mail->setFrom($this->settings->title." <".$this->settings->email.">")
	        ->addTo($email)
	        ->setSubject("Polagraph - rezervace na ".$day->format("d. m. Y"))
	        ->setHtmlBody($template);

	    $mailer = new SendmailMailer;
	    $mailer->send($mail);
    }
    
    public function checkFolders(){
		foreach($this->branches as $branch){
			$labFolders = $this->getGfolders($branch->folder);
			$unasignedFolders = $this->projectManager->getUnasigned($branch->id);
			foreach($labFolders as $labFolderId=>$labFolderName){
				if(!empty($unasignedFolders[$labFolderName])){
					//get project root folder and check subfolders if exists
					$subFolders = $this->getGfolders($labFolderId);
					foreach($subFolders as $subfolderId=>$subfolderName){
						if($subfolderName==$branch->subfolder){
							$this->projectManager->update(array("hash"=>$subfolderId), $unasignedFolders[$labFolderName]);
						}
					}
				}
			}
		}
    }


    public function removeZips(){
    	$rootFolder = DATA_DIR."/temp";
		foreach (Finder::findFiles('*.*')->from($rootFolder) as $key => $file) {
			unlink($key);
		}

	}


    /*
    public function moveFromFolders(){
		$labFolders = $this->getGfolders(G_ROOT);
		foreach($labFolders as $labFolderId=>$labFolderName){
			$existsProject = $this->projectManager->findByLabId($labFolderName);
			if($existsProject){
				$projectHash = $existsProject->hash;
				$files = $this->getGfiles($labFolderId);
				foreach($files as $file){
					$this->moveGfile($file, $projectHash);
				}
				$files = $this->getGfiles($labFolderId);
				if(empty($files)){
					$this->projectManager->update(array("labId"=>NULL), $existsProject->id);
					$this->deleteGfolder($labFolderId);
				}
			}
		}
    }
    */


}
