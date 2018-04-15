<?php

namespace Vshapovalov\Crud\Http\Controllers;

use Illuminate\Http\File;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaController extends BaseController
{
	private $fileTypes;
	private $settings;

	function __construct() {
		$this->fileTypes = ['jpeg', 'jpg', 'png', 'gif', 'ico'];
		$this->settings = config('cruds.media_default_settings', []);

		$this->middleware('auth');
	}

	function putMedia(request $request){

		$parentFolder = $request->input('path', 'uploads');

		$files = [];

		$media_settings = json_decode($request->input('media_settings','{}'), true);

		if (count($media_settings))
			$this->settings = $media_settings;

		foreach ($request->allFiles() as $file){

			$fileName = $file->getClientOriginalName();

			$files[] = $fileName;

			$fullFileName = $fileName;

			$ext = pathinfo($fullFileName, PATHINFO_EXTENSION);

			if (in_array($ext, $this->fileTypes) && count($this->settings)){

				$image = Image::make($file);

				$image->backup();

				$quality = null;

				if (isset($this->settings['resize'])){

					$quality = isset($this->settings['resize']['quality']) ? $this->settings['resize']['quality'] : $quality;

					if (isset($this->settings['resize']['width']) || isset($this->settings['resize']['height'])){
						$image->resize(
							isset($this->settings['resize']['width']) ? $this->settings['resize']['width'] : null,
							isset($this->settings['resize']['height']) ? $this->settings['resize']['height'] : null,
							function($constraint){
								$constraint->aspectRatio();
								$constraint->upsize();
							}
						)->encode($ext, $quality);
					}

					$image->backup();
				}

				Storage::disk('public')->put($parentFolder . '/' . $fullFileName, $image->stream());

				if (isset($this->settings['thumbnails'])){
					foreach ($this->settings['thumbnails'] as $thumb){

						$image->reset();

						$thumbFilename = substr($fullFileName, 0, strrpos($fullFileName, '.'.$ext))
						                 . '-'.$thumb['name'] . '.' . $ext;

						$width = $image->getWidth();

						if (isset($thumb['scale'])){

							$image->resize(
								round($width * ($thumb['scale'] / 100)),
								null,
								function($constraint){
									$constraint->aspectRatio();
									$constraint->upsize();
								}
							);
						}

						if (isset($thumb['crop'])){

							$image->crop(
								$thumb['crop']['width'],
								$thumb['crop']['height']
							);
						}

						if (isset($thumb['fit'])){

//						    	position
//							    top-left, top, top-right, left, center (default), right, bottom-left,
//								bottom, bottom-right

							$image->fit($thumb['fit']['width'], $thumb['fit']['height'], function ($constraint) {
								$constraint->upsize();
							}, isset($thumb['fit']['position'])? $thumb['fit']['position'] : 'center');

						}

						Storage::disk('public')->put($parentFolder . '/' . $thumbFilename, $image->stream());

					}
				}

				$image->destroy();

			} else {

				Storage::disk('public')->putFileAs($parentFolder, $file, $fullFileName);
			}
		}

		return ['success' => true, 'message' => implode(', ', $files)];
	}

    function getItems(Request $request){

	    $path = 'uploads';

        $type = $request->get('source', 'both');
        $savePath  = $request->get('save_path', true);

        debug('$type');
        debug($type);

        debug('$savePath');
        debug($savePath);

	    if (($parentDir = $request->input('path', false)) && is_string($parentDir) &&

	        Storage::disk('public')->exists($parentDir)){

		    $path = $parentDir;

	    }

	    if ($savePath && $path ) {
		    Session::put('media.path', $path);
	    }

		setlocale(LC_ALL,'en_US.UTF-8');

        $files = [];
        $dirs = [];

        if ($type == 'both' || $type == 'file'){
            $files = array_map(function($f){

                $pi = pathinfo($f);
                $pi['type'] = 'file';

                return $pi;

            }, Storage::disk('public')->files($path));
        }

        if ($type == 'both' || $type == 'dir'){
            $dirs = array_map(function($f){

                $pi = pathinfo($f);
                $pi['type'] = 'folder';

                return $pi;

            } , Storage::disk('public')->directories($path));
        }



	    return array_merge( $dirs, $files);
    }

    function moveItems(){

        $response = [
            'status' => 'success'
        ];

        $items = request()->get('items', []);

        $path = request()->get('path', 'uploads');

        foreach ($items as $item){

            Storage::disk('public')->move($item['dirname'] .'/'. $item['basename'], $path.'/'. $item['basename']);
        }

        return $response;
    }

	function renameFolder(Request $request){

		$response = [
			'status' => 'success'
		];

		$params = $request->only(['item', 'title']);

		if (!Storage::disk('public')->exists($params['item']['dirname'] . '/' . $params['title'])){
			Storage::disk('public')->move(
				$params['item']['dirname'] . '/' . $params['item']['basename'],
				$params['item']['dirname'] . '/' . $params['title']
			);
		}

		return $response;

	}

	function newFolder(Request $request){

		$response = [
			'status' => 'success'
		];

		$params = $request->only(['root', 'title']);

		if (!empty($params['root']) && !Storage::disk('public')->exists($params['root'] . '/' . $params['title'])){
			Storage::disk('public')->makeDirectory($params['root'] . '/' . $params['title']);
		}

		return $response;
	}

	function deleteItem(Request $request){
		$response = [
			'status' => 'success'
		];

		if ($item = $request->input('item', false)){

			if ($item['type'] == 'folder'){
				Storage::disk('public')->deleteDirectory($item['dirname'] . '/' . $item['basename']);
			} else {
				Storage::disk('public')->delete($item['dirname'] . '/' . $item['basename']);
			}
		}

		return $response;
	}
}
