<?php

namespace Vshapovalov\Crud\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Vshapovalov\Crud\Facades\Crud;

class CreateCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'crud:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create crud form';

	protected $crudFormFields = [
		'name' => null, 'code' => null, 'model' => 'App\\', 'id' => 'id', 'display' => 'title', 'type' => 'list'
	];

	protected $crudFieldFields = [
		'name' => null, 'caption' => null, 'type' => 'textbox',
		'visibility' => '[ "browse", "add", "edit" ]',
		'by_default' => null, 'json' => 0, 'readonly' => 0,
		'description' => null, 'tab' => 'Основные параметры',
		'validation' => null, 'additional' => '{}', 'order' => 1,
		'columns' => 6
	];

	protected $crudScopeFields = [
		'name' => null
	];

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get the composer command for the environment.
	 *
	 * @return string
	 */
	protected function findComposer()
	{
		if (file_exists(getcwd().'/composer.phar')) {
			return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
		}

		return 'composer';
	}

	protected function askCrudFormInfo( $meta ){

		$this->info('Setting crud form params ...');

		return $this->askParamSet( $meta );

	}

	function askParam( $key, $paramMeta ){

		$val = $this->ask($key, ($paramMeta === null) ? 'empty' : (string)$paramMeta );

		return ($val != 'empty') ? $val : null;
	}

	function askParamSet( $paramSet ){

		$result = [];

		foreach( $paramSet as $key=>$paramMeta){

			$result[$key] = $this->askParam( $key, $paramMeta);
		}

		return $result;
	}

	protected function askCrudFieldsInfo( $meta ){

		$this->info('Setting form fields ...');

		$inputFields = [];

		for(;;){
			$inputFields[] = $this->askParamSet( $meta );
			if ( !$this->confirm( 'Create one more field?', 'yes') ) break;
		}

		return $inputFields;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle(Filesystem $filesystem)
	{
		$this->info('Hi, let\'s create crud form!');

		$form = null;

		if ($this->hasArgument('table')){
			$this->info( $this->argument('table') );
		} else {

			$form = $this->askCrudFormInfo( $this->crudFormFields );

			if ( $this->confirm('Want add fields?', 'yes') )
				$form['fields'] = $this->askCrudFieldsInfo( $this->crudFieldFields );

			if ( $this->confirm('Want add scopes?', 'yes') )
				$form['scopes'] = $this->askCrudFieldsInfo( $this->crudScopeFields );
		}

		if ($form)
		  Crud::createForm( $form );

		$this->info('Crud form successfully created!');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire(Filesystem $filesystem)
	{
		$this->handle($filesystem);
	}
}
