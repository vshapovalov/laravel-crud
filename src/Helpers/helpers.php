<?php

if(!function_exists('crud_menu')){

	function crud_menu($code){

		return Crud::menu($code);
	}
}

if(!function_exists('crud_image')){

	function crud_image($items, $isArray = false){

		return Crud::image($items, $isArray);
	}
}

if(!function_exists('crud_image_thumb')){

	function crud_image_thumb($items, $thumbName){

		return Crud::imageThumb($items, $thumbName);
	}
}

if(!function_exists('crud_settings')){

	function crud_settings($settingCode){

		return Crud::settings($settingCode);
	}
}