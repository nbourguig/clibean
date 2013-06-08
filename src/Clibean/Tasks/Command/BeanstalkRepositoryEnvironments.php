<?php
namespace Clibean\Tasks\Command;

use Clibean\Components\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeanstalkRepositoryEnvironments extends BeanstalkCommand
{

    protected function configure()
    {
        $description = '';
        $description .= 'List repository environments of the current repository, if no repository is specified as argument.';

        $this
            ->setName('repo:envs')
            ->setDescription($description)
            ->addArgument(
                'repository',
                InputArgument::OPTIONAL,
                'Repository name'
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $repo = $this->getCurrentRepoUnlessSpecified($input);

        // Title
        $this->title('Getting environment list');

        // Make call
        $this->uri = "/$repo/server_environments.json";
        $data      = $this->get();

        // Display
        if (empty($data))
        {
            $this->error('No environment was found.');
            return;
        }

        $this->displayAsTable($data);
    }

    protected function display($env)
    {
        $repo_line = sprintf('[%s] on branch [%s] ->  #%s : %s', $env['name'], $env['branch_name'], $env['id'], $env['current_version']);
        $this->output->writeln($repo_line);
        return true;
    }

    protected function displayAsTable($environments)
    {
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('Name', 'Branch', 'Automatic ?', 'Current commit'));
        foreach ($environments as $env)
        {
            if (empty($env['server_environment']))
            {
                continue;
            }

            $e = $env['server_environment'];
            $table->addRow(array(
                $e['name'],
                $e['branch_name'],
                empty($e['automatic']) ? '' : 'X',
                $e['current_version'],
            ));
        }

        $table->render($this->output);
    }

}

