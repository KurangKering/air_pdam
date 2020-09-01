<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadManager  {

	const DIRNAME_METERAN = "uploads/meteran/";
	const DIRNAME_PEMBAYARAN = "uploads/pembayaran/";

	public function __construct()
	{
		$this->CI =& get_instance();
		
	}

	public static function getFullPathMeteran($filename = NULL)
	{
		if ($filename == NULL) {
			return NULL;
		}
		return base_url(self::DIRNAME_METERAN.$filename);
	}
	
	public static function getFullPathPembayaran($filename = NULL)
	{
		if ($filename == NULL) {
			return NULL;
		}
		return base_url(self::DIRNAME_PEMBAYARAN.$filename);
	}

	public function makeNotExistDirectory($dirname)
	{
		if (!is_dir($dirname)) {
			mkdir("./{$dirname}", 0777, TRUE);
		}
		return TRUE;
		
	}
	public function uploadMeteran($file,$filename = NULL, $oldname = null)
	{
		$dirname = self::DIRNAME_METERAN;
		$response = $this->uploadImage($file,$dirname,$filename, $oldname);
		return $response;

	}

	public function uploadPembayaran($file,$filename = NULL, $oldname = null)
	{
		$dirname = self::DIRNAME_PEMBAYARAN;
		$response = $this->uploadImage($file,$dirname,$filename, $oldname);
		return $response;

	}


	public function removeMeteran($filename)
	{
		$this->removeFile(self::DIRNAME_METERAN.$filename);
	}

	public function removeFile($fullPath)
	{
		return unlink($fullPath);
	}


	public function uploadImage($field, $dirname, $filename = NULL, $oldname = null)
	{
		$this->makeNotExistDirectory($dirname);
		$config['upload_path']          = $dirname;
		$config['allowed_types']        = 'gif|jpg|jpeg|png|pdf';
		if ($filename == NULL) {
			$config['encrypt_name']        = TRUE;
		} else {
			$config['file_name']        = $filename;
			$config['overwrite']        = TRUE;

		}
		

		$this->CI->load->library('upload');
		$this->CI->upload->initialize($config);
		$response = array();
		if ( ! $this->CI->upload->do_upload($field))
		{

			$response['success'] = "N";
			$response['error'] = $this->CI->upload->display_errors();

		}
		else
		{

			$response['success'] = "Y";
			$response['upload_data'] = $this->CI->upload->data();

			if ($oldname != NULL && ($response['upload_data']['file_name'] != $oldname)) {
				@chmod(FCPATH.$dirname.$oldname, 0777);
				@unlink(FCPATH.$dirname.$oldname);
			}
		}
		return $response;
	}

}

/* End of file UploadManager.php */
/* Location: ./application/libraries/UploadManager.php */