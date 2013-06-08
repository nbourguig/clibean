<?php
namespace Clibean\Tasks\Command;

use Clibean\Components\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BeanstalkRepositoryReleases extends BeanstalkCommand
{

    protected function configure()
    {
        $description = '';
        $description .= 'Get releases of repository ';

        $this
            ->setName('repo:releases')
            ->setDescription($description);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $repo = $this->getRepo();

        // Title
        $this->title('Current repo releases');

        // Make call
        $this->uri = "/$repo/releases.json";
        $data      = $this->get();

        // Display
        if (empty($data))
        {
            $this->info('No releases found for this repo.');
            return;
        }

        $this->output->writeln("");
        $this->output->writeln("");
        foreach ($data as $release)
        {
            if (!empty($release['release']))
            {
                $this->display($release['release']);
            }
        }
    }

    protected function display($release)
    {
        $this->output->writeln("#" . $release['id'] . " : " . $release['created_at']);
        $this->output->writeln('------------------------------------------------------');
        $this->output->writeln('By : ' . $release['author']);
        $this->output->writeln('to : ' . $release['environment_name']);
        $this->output->writeln('State : ' . $release['state']);
        $this->output->writeln('Commit: ' . $release['environment_revision']);
        $this->output->writeln("");
    }

}

