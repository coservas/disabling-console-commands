<?php

namespace App\Symfony;

use Symfony\Bundle\FrameworkBundle\Console\Application as SymfonyApplication;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Yaml\Exception\ParseException;

class Application extends SymfonyApplication
{
    protected const NAME_CONFIG = 'disabled_commands';
    protected $disabledCommands = null;

    protected function loadConfig()
    {
        if ($this->disabledCommands !== null) {
           return;
        }

        try {
            $filename = sprintf('config/%s.yaml', self::NAME_CONFIG);
            $data = Yaml::parseFile($filename);

            // If the configuration file does not exist,
            // then all available commands are simply displayed

            if ((null === $data) ||
                (false === isset($data[ self::NAME_CONFIG ])) ||
                (null === $data[ self::NAME_CONFIG ])
            ) {
                $this->disabledCommands = [];
                return;
            }

            $cmds = $data[ self::NAME_CONFIG ];
            foreach ($cmds as $cmd) {
                $this->disabledCommands[(string)$cmd] = true;
            }
        } catch (ParseException $e) {
            $this->disabledCommands = [];
        }
    }

    public function add(Command $command)
    {
        $this->loadConfig();

        $name = $command->getName();
        if (isset($this->disabledCommands[$name])) {
            return;
        }

        return parent::add($command);
    }
}