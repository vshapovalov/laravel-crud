<?php

namespace Vshapovalov\Crud\Http\Controllers;

use Illuminate\Http\File;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

	function processImage($image, $options){

        $width = $image->getWidth();

        $quality = null;

		if (isset($options['crop'])){

			if (!isset($options['crop']['x'])) $options['crop']['x'] = null;
			if (!isset($options['crop']['y'])) $options['crop']['y'] = null;

			$image->crop(
				$options['crop']['width'],
				$options['crop']['height'],
				$options['crop']['x'],
				$options['crop']['y']
			);
		}

        if (isset($options['resize'])){

            $quality = isset($options['resize']['quality']) ? $options['resize']['quality'] : $quality;

            if (isset($options['resize']['width']) || isset($options['resize']['height'])){
                $image->resize(
                    isset($options['resize']['width']) ? $options['resize']['width'] : null,
                    isset($options['resize']['height']) ? $options['resize']['height'] : null,
                    function($constraint){
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    }
                )->encode(null, $quality);
            }
        }

        if (isset($options['scale'])){

            $image->resize(
                round($width * ($options['scale'] / 100)),
                null,
                function($constraint){
                    $constraint->aspectRatio();
                    $constraint->upsize();
                }
            );
        }

        if (isset($options['fit'])){

//						    	position
//							    top-left, top, top-right, left, center (default), right, bottom-left,
//								bottom, bottom-right

            $image->fit($options['fit']['width'], $options['fit']['height'], function ($constraint) {
                $constraint->upsize();
            }, isset($options['fit']['position'])? $options['fit']['position'] : 'center');

        }

    }

    function cropImage(){

        if (($item = request('item', false)) && request()->has('crop_data')) {

            $image = Image::make(
                Storage::disk('public')->get($item['dirname'] .'/'. $item['basename'])
            );

            $fileName = pathinfo($item['basename'], PATHINFO_FILENAME);
            $fileExt = pathinfo($item['basename'], PATHINFO_EXTENSION);

            $this->processImage($image, [ 'crop' => request('crop_data') ] );

            Storage::disk('public')
                ->put($item['dirname'] .'/'. $fileName . '-cropped.' . $fileExt, $image->stream());

            $image->destroy();
        }
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

			if (request()->has('crop_data')){
			    //cropper.js data
                $this->settings['crop'] = json_decode(request('crop_data'), true);
            }

			if (in_array($ext, $this->fileTypes) && count($this->settings)){

				$image = Image::make($file);

                $this->processImage($image, $this->settings);

                $image->backup();

				Storage::disk('public')->put($parentFolder . '/' . $fullFileName, $image->stream());

				if (isset($this->settings['thumbnails'])){

					foreach ($this->settings['thumbnails'] as $thumb){

						$image->reset();

						$thumbFilename = substr($fullFileName, 0, strrpos($fullFileName, '.'.$ext))
						                 . '-'.$thumb['name'] . '.' . $ext;

						$this->processImage($image ,$thumb);

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

		if ($items = $request->input('items', false)){

		    foreach ($items as $item){
                if ($item['type'] == 'folder'){
                    Storage::disk('public')->deleteDirectory($item['dirname'] . '/' . $item['basename']);
                } else {
                    Storage::disk('public')->delete($item['dirname'] . '/' . $item['basename']);
                }
            }
		}

		return $response;
	}
}
