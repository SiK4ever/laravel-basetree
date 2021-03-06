<?php


namespace BaseTree\Console\Generators;


use File;
use Illuminate\Console\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;

abstract class BaseGenerator extends Command
{
    const OPTION_FOLDER = 'folder';
    const OPTION_NAMESPACE = 'namespace';
    const OPTION_NAME = 'name';

    const KEY_MODEL_NAMESPACE = 'model-namespace';
    const KEY_MODEL_NAME = 'model-name';
    const KEY_DAL_NAMESPACE = 'dal-namespace';
    const KEY_DAL_NAME = 'dal-name';
    const KEY_DAL_INTERFACE_NAMESPACE = 'dal-interface-namespace';
    const KEY_DAL_INTERFACE_NAME = 'dal-interface-name';
    const KEY_BLL_NAMESPACE = 'bll-namespace';
    const KEY_BLL_NAME = 'bll-name';
    const KEY_CONTROLLER_NAMESPACE = 'controller-namespace';
    const KEY_CONTROLLER_NAME = 'controller-name';

    protected $modifiers = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->validateOptions();
        $this->extractModifiers();
        $this->go();
    }

    protected function validateOptions()
    {
        $options = array_except($this->options(), ['help', 'quiet', 'verbose', 'version', 'ansi', 'no-ansi', 'no-interaction', 'env']);

        foreach($options as $key => $value) {
            if(empty($value)) {
                throw new InvalidArgumentException("Option --{$key} should not be empty");
            }
        }
    }

    protected function setModifiers(array $modifiers, bool $compile = false): void
    {
        $this->modifiers = $modifiers;

        if ($compile) {
            foreach ($this->modifiers as $key => $modifier) {
                $this->modifiers[$key] = $this->modify($modifier);
            }
        }
    }

    protected function extractParentDependency(string $parent): array
    {
        # Instance just to check if the given namespace exists
        $instance = app($parent);
        # Extract just the namespace
        $modelNamespace = substr($parent, 0, strrpos($parent, "\\"));
        # Extract just the name
        $modelName = substr($parent, strrpos($parent, "\\") + 1);

        return [$modelName, $modelNamespace];
    }

    protected function modify(string $input): string
    {
        foreach ($this->modifiers as $key => $modifier) {
            $input = str_replace("[{$key}]", $modifier, $input);
        }

        return $input;
    }

    protected function writeFromStub(string $stubPath, string $folder, string $fileNameKey): string
    {
        $fileName = "{$folder}/{$this->modifiers[$fileNameKey]}.php";
        if (File::exists($fileName)) {
            $this->warn("{$fileName} is already created.");

            return '';
        }

        $stubData = $this->modify(File::get($stubPath));

        if ( ! File::exists($folder)) {
            File::makeDirectory($folder, 0775, true, true);
        }

        File::put($fileName, $stubData);

        return $fileName;
    }

    public function returnNamespace(string $namespace): string
    {
        return str_replace('/', '\\', $namespace);
    }

    abstract protected function extractModifiers();

    abstract protected function go();
}