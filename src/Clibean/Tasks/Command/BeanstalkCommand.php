<?php
namespace Clibean\Tasks\Command;

use Guzzle\Plugin\CurlAuth\CurlAuthPlugin;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Clibean\Tasks\Command;
use Guzzle\Http\Client;
use Clibean\Components\Config;


class BeanstalkCommand extends AbstractCommand
{
    protected $client;
    protected $request;
    protected $endpoint;
    protected $uri;


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // Client
        $this->createClient();
    }


    protected function createClient()
    {
        $this->client = new Client($this->getEndpoint());


        // Basic auth
        $authPlugin = new CurlAuthPlugin(
            Config::get('clibean.auth.username'),
            Config::get('clibean.auth.password')
        );
        $this->client->addSubscriber($authPlugin);

        // Default headers
        return;
        $this->client->setDefaultHeaders(array(
            'Content-Type' => 'application/json',
            'User-Agent'   => 'clibean',
        ));
    }

    protected function getEndpoint()
    {
        return sprintf('https://%s.beanstalkapp.com',
            Config::get('clibean.account'));
    }

    protected function get()
    {
        $uri           = $this->getMethodUri();
        $this->request = $this->client->get($uri);
        $response      = $this->request->send();
        return $response->json();
    }


    protected function getMethodUri()
    {
        return '/api' . $this->uri;
    }

    protected function echoUrl()
    {
        echo  $this->request->getUrl();
    }

    protected function getRepo()
    {
        // Repo
        $repo = Config::get('clibean.project.repo');
        if (empty($repo))
        {
            $this->error('No repo is configured ! check your clibean.json file');
        }

        return $repo;
    }
}