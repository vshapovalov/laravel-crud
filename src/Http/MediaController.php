<?php

namespace Vshapovalov\Crud\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Vshapovalov\Crud\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaController extends BaseController
{
	private $fileTypes;
	private $settings;

	function __construct() {
		$this->fileTypes = ['jpeg', 'jpg', 'png'];

		$this->settings = [
			'resize' => [
				'width' => 1000,
				'height' => null,
				'quality' => 90
			],
			'thumbnails' => [
				[
					'name' => 'medium',
					'scale' => 50
				],
				[
					'name' => 'small',
					'scale' => 25
				],
				[
					'name' => 'cropped',
					'crop' => [
						'width' => 250,
						'height' => 250,
					]
				]
			]
		];

		$this->middleware('auth');
	}

	function putMedia(request $request){

    	debug($request->input('root_id'));

    	if ($request->input('root_id') == 'root'){
		    $parentFolder = MediaItem::whereNull('parent_id')->first();
	    } else {
		    $parentFolder = MediaItem::whereId($request->input('root_id'))->first();
	    }

	    $files = [];

		foreach ($request->allFiles() as $file){

			$fileName = $file->getClientOriginalName();

			$files[] = $fileName;

		    $item = MediaItem::create([
			    'title' => $fileName,
			    'path' => '',
			    'type' => 'item',
			    'parent_id' => $parentFolder->id
		    ]);

			$item->path = $parentFolder->path . str_pad($item->id, 4, '0', STR_PAD_LEFT);
			$item->save();

			$fullFileName = $item->id.'-'.$fileName;

    		Storage::disk('public')->putFileAs('uploads', $file, $fullFileName);

			$ext = pathinfo(storage_path().'/app/public/uploads/' . $fullFileName, PATHINFO_EXTENSION);

    		if (in_array($ext, $this->fileTypes)){

			    if (count($this->settings)){

				    $image = Image::make($file);

				    $image->backup();

				    $quality = null;

			    	if (isset($this->settings['resize'])){

			    		$image->resize(
					    	$this->settings['resize']['width'],
					        $this->settings['resize']['height'],
					        function($constraint){
						        $constraint->aspectRatio();
						        $constraint->upsize();
					        }
				        );

					    $quality = isset($this->settings['resize']['quality']) ? $this->settings['resize']['quality'] : $quality;
					    $image->backup();

					    $image->save(
					    	storage_path().'/app/public/uploads/' . $fullFileName, $quality);
				    }

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

						    $image->save(
							    storage_path().'/app/public/uploads/' . $thumbFilename,
							    $quality);

					    }
				    }

				    $image->destroy();
			    }

		    }


	    }

	    return ['success' => true, 'message' => implode(', ', $files)];
    }

    function getItems(Request $request){

	    $items = MediaItem::orderBy('type');

	    $rootFolder = $request->input('root', 'root');
	    $path = $request->input('path', false);

	    if ($path && count($path)) {

		    Session::put('media.path', $path);
	    }

    	if ($rootFolder == 'root') {
		    return $items->whereNull('parent_id')->first()->children()->orderBy('type')->get();
	    } else {
		    return $items->whereParentId($rootFolder)->get();
	    }

    }

	function renameFolder(Request $request){

		$response = [
			'status' => 'success'
		];

		$params = $request->only(['item', 'title']);

		MediaItem::whereId($params['item']['id'])->update(['title' => $params['title']]);

		return $response;

	}

	function newFolder(Request $request){

		$response = [
			'status' => 'success'
		];

		$params = $request->only(['root', 'title']);

		if ($params['root']['id'] == 'root'){
			$parentFolder = MediaItem::whereNull('parent_id')->first();
		} else {
			$parentFolder = MediaItem::whereId($params['root']['id'])->first();
		}

		$folder = MediaItem::create([
			'title' => $params['title'],
			'level' => 0,
			'path' => '',
			'parent_id' => $parentFolder->id,
			'type' => 'folder']);

		$folder->path = $parentFolder->path . str_pad($folder->id, 4, '0', STR_PAD_LEFT) . ';';
		$folder->save();

		return $response;
	}

	function deleteItem(Request $request){
		$response = [
			'status' => 'success'
		];

		$item = $request->input('item', null);

		if($item = MediaItem::whereId($item['id'])->first()){

			if ($item->type == 'item'){
				MediaItem::whereId($item->id)->delete();
				Storage::disk('public')->delete('uploads/'.$item->id.'-'.$item->title);
			}

			if ($item->type == 'folder'){

				$fileItems = MediaItem::where('path','like','%'.str_pad($item->id, 4, '0',STR_PAD_LEFT) . ';'.'%')->whereType('item')->get();

				forEach($fileItems as $fileItem){
					Storage::disk('public')->delete('uploads/'.$fileItem->id.'-'.$fileItem->title);
				}

				MediaItem::where('path','like','%'.str_pad($item->id, 4, '0',STR_PAD_LEFT) . ';'.'%')->delete();
			}

		}

		return $response;
	}
}
