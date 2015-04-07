<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class DownloadController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function download($lnk = null)
	{
		$reRouting = false;
		
		if(\Session::has('downloadlink')) {
			if(\Session::get('downloadlink', null) == $lnk) {
				$lnk = \Session::get('downloadlinkR', null);
				$reRouting = true;
				\Session::forget('downloadlink');
			}
		}
		
		$denyDownload = true;
		
		$checkLink = \Veer\Models\Download::where('secret','=',$lnk)->first();
		
		if(is_object($checkLink)) {
		
			if($checkLink->expires == false) { $denyDownload = false; } else {
				
				$expired_times = false; $expired_day = false;
				
				if($checkLink->expiration_times > 0 && $checkLink->downloads >= $checkLink->expiration_times) {				
					$expired_times = true;
				} 

				if($checkLink->expiration_day > \Carbon\Carbon::create(2000) && now() > $checkLink->expiration_day) {
					$expired_day = true;
				}
				
				if($expired_times == false && $expired_day == false) { $denyDownload = false; }
			}
		} 
		
		if($denyDownload == true)  {

			return Redirect::route('index');
			
		} else {
			
			if($reRouting == true) {
				$checkLink->increment('downloads');
				return $this->downloadingLocalOrCloudFiles($checkLink->fname);
			}
			
			$newLink = "sessionLink".str_random(64);
			\Session::put('downloadlink', $newLink);
			\Session::put('downloadlinkR', $lnk);
			return Redirect::route('download.link', $newLink);
		}	
	}

        protected function downloadingLocalOrCloudFiles($fname)
        {
            if(!config('veer.use_cloud_files')) {
                return \Response::download( storage_path().'/app/'.config('veer.downloads_path') . "/" . $fname );
            } else {
                return redirect(config('veer.cloudstorage_path').'/'.config('veer.downloads_path').'/'.$fname);
            }
        }
}
