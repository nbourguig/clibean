<?php
namespace Clibean\Tasks\Command;

use Clibean\Components\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BeanstalkUserMe
 * @package Clibean\Tasks\Command
 *
 * @see http://api.beanstalkapp.com/user.html
 */
class BeanstalkUserMe extends BeanstalkCommand
{

    protected function configure()
    {
        $description = '';
        $description .= 'Get infos about current repository ';

        $this
            ->setName('user:me')
            ->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Title
        $this->title('Current user infos');

        // Make call
        $this->uri = '/users/current.json';
        $data      = $this->get();

        // Display
        if (empty($data['user']))
        {
            $this->error('User data not found.');
            return;
        }

        $this->table($data['user']);
    }

}