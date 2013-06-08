<?php

/**
 * Wrapper to clibean Json project file.
 *
 */


namespace Clibean\Components\Json;

use JsonSchema\Validator;
use Clibean\Components\Config;


class JsonFile
{

    /**
     * Parse clibean.json and expose it as Component\Config
     * under `clibean` key
     *
     * @throws \Exception
     */
    public function parse()
    {
        $clibeanFile = $this->getClibeanFile();
        $data        = json_decode(file_get_contents($clibeanFile));

        $schemaFile = Config::getApplicationDirectory() . '/resources/clibean-schema.json';
        $schemaData = json_decode(file_get_contents($schemaFile));

        $validator = new Validator();
        $validator->check($data, $schemaData);

        if (!$validator->isValid())
        {
            $errors = array();
            foreach ((array)$validator->getErrors() as $error)
            {
                $errors[] = ($error['property'] ? $error['property'] . ' : ' : '') . $error['message'];
            }

            throw new \Exception('"' . $clibeanFile . '" does not match the expected JSON schema (' . json_encode($errors) . ')');
        }


        // Just feed Laravel config, so we can do thing like
        // Config::get('clibean.auth.password')
        Config::set('clibean', $this->toArray($data));
    }

    protected function getClibeanFile()
    {
        $file = Config::getWorkingDirectory() . '/clibean.json';
        if (!file_exists($file))
        {
             throw new \Exception('No clibean.json file found');
        }
    }

    /**
     * Turns Object (nested) into assoxiative array
     * to ease integration with Laravel Config style.
     *
     * @param $object
     * @return array
     */
    protected function toArray($object)
    {
        if (is_array($object) || is_object($object))
        {
            $result = array();
            foreach ($object as $key => $value)
            {
                $result[$key] = $this->toArray($value);
            }
            return $result;
        }
        return $object;
    }

}