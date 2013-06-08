<?php
namespace Clibean\Tasks\Command;

use Clibean\Components\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeanstalkRepositoryInfos extends BeanstalkCommand
{

    protected function configure()
    {
        $description = '';
        $description .= 'Get infos about current repository, if no repository is specified as argument.';

        $this
            ->setName('repo:infos')
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
        $this->title('Current repo infos');

        // Make call
        $this->uri = "/repositories/$repo.json";
        $data      = $this->get();

        // Display
        if (empty($data['repository']))
        {
            $this->error('Repository data not found.');
            return;
        }

        $this->table($data['repository']);
    }

}

