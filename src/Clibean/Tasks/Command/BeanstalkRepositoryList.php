<?php
namespace Clibean\Tasks\Command;

use Clibean\Components\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeanstalkRepositoryList extends BeanstalkCommand
{

    protected function configure()
    {
        $description = '';
        $description .= 'List repositories. We can give it a keyword as argument to filter out results.';

        $this
            ->setName('repo:list')
            ->setDescription($description)
            ->addArgument(
                'keyword',
                InputArgument::OPTIONAL,
                'Simple keyword to mach repository name against ...'
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $repo = $this->getRepo();

        // Keyword search ?
        $keyword = $input->getArgument('keyword');

        // Title
        $this->title('Getting respositories list');

        // Make call
        $this->uri = "/repositories.json";
        $data      = $this->get();

        // Display
        if (empty($data))
        {
            $this->error('No repository was found.');
            return;
        }

        $count = 0;
        foreach ($data as $repo)
        {
            if (empty($repo['repository']))
            {
                continue;
            }

            if ($this->display($repo['repository'], $keyword))
            {
                $count++;
            }
        }


        if ($count > 0)
        {
            $this->info("\n > Displayed $count repositories.");
        }
        else
        {
            $this->info("\n > No repository found.");
        }

    }

    protected function display($repo, $filterNameBy)
    {
        if ($filterNameBy)
        {
            if (!substr_count($repo['name'], $filterNameBy))
            {
                return false;
            }
        }

        $repo_line = sprintf('[%s] #%s : %s', $repo['vcs'], $repo['id'], $repo['name']);
        $this->output->writeln($repo_line);
        return true;
    }

}

