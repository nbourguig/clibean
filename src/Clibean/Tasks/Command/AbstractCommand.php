<?php
namespace Clibean\Tasks\Command;

use Clibean\Components\Config;
use Clibean\Components\Json\JsonFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected $output;
    protected $jsonFile;

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Output
        $this->output = $output;

        // Json file
        try
        {
            $this->jsonFile = new JsonFile();
            $this->jsonFile->parse();
        }
        catch (\Exception $e)
        {
            $this->error("" . $e->getMessage());
        }
    }


    //
    // Output helper functions
    //

    public function output($message)
    {
        $this->output->writeln($message);
    }

    public function title($title)
    {
        $this->output->writeln("");
        $this->output->writeln("");
        $this->output->writeln("-------------------------------------------------");
        $this->output->writeln(" $title");
        $this->output->writeln("-------------------------------------------------");
    }

    public function info($message, $space = false)
    {
        $message = $this->space($space) . ' ' . $message;
        $this->output->writeln("<info>$message</info>");
    }


    public function warn($message, $space = false)
    {
        $message = $this->space($space) . '[WARN] ' . $message;
        $this->output->writeln("<comment>$message</comment>");
    }

    public function error($message, $exitOnError = true, $space = false)
    {
        $message = $this->space($space) . '[ERROR] ' . $message;
        $this->output->writeln("<error>$message</error>");

        if ($exitOnError)
        {
            die("[ERROR] Execution aborted!\n");
        }
    }

    public function debug($var, $dump = false, $space = false)
    {
        $this->output->writeln($this->space($space));
        if ($dump)
        {
            var_dump($var);
        }
        else
        {
            print_r($var);
        }
        $this->output->writeln("");
    }

    public function table($data)
    {
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('Property', 'Value'));
        foreach ($data as $key => $value)
        {
            $table->addRow(array($key, $value));
        }

        $table->render($this->output);
    }

    private function space($space)
    {
        return $space ? "\n\n\n" : "";
    }

}