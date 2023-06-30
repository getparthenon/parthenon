<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2020-2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://getparthenon.com/docs/next/license.
 *
 * Change Date: 26.06.2026 ( 3 years after 2.2.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace Parthenon\Athena\Command;

use Parthenon\Common\LoggerAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'parthenon:athena:generate-entity-section', aliases: ['p:a:ges'])]
class GenerateEntitySectionCommand extends Command
{
    use LoggerAwareTrait;

    public function __construct(private string $projectRoot)
    {
        parent::__construct(null);
    }

    protected function configure()
    {
        $this
            ->addArgument('entity', InputArgument::REQUIRED, 'The name of the entity to be created')
            ->addArgument('config_type', InputArgument::REQUIRED, 'What type of doctrine configuration type that should be used. Annotations or Attributes');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('entity')) {
            $entityQuestion = new Question('Please provide the name of the entity: ');
            $entityQuestion->setValidator(function ($entity) {
                if (empty($entity)) {
                    throw new \Exception('Email can not be empty');
                }

                if (!preg_match('~^[A-Za-z_]+$~isu', $entity)) {
                    throw new \Exception('Entity can only be letters and an underscore');
                }

                return $entity;
            });

            $email = $this->getHelper('question')->ask($input, $output, $entityQuestion);

            $input->setArgument('entity', $email);
        }

        if (!$input->getArgument('config_type')) {
            $configTypeQuestion = new Question('Which type of config: [annotations] ', 'annotations');
            $configTypeQuestion->setValidator(function ($configType) {
                if (empty($configType)) {
                    throw new \Exception('Config type can not be empty');
                }

                $configType = strtolower($configType);

                if (!in_array($configType, ['annotations', 'attributes'])) {
                    throw new \Exception('Config type must be either "annotations" or "attributes"');
                }

                return $configType;
            });

            $role = $this->getHelper('question')->ask($input, $output, $configTypeQuestion);
            $input->setArgument('config_type', $role);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityName = $input->getArgument('entity');
        $configType = $input->getArgument('config_type');

        $camelCaseToSnakeCase = function ($input) {
            if (0 === preg_match('/[A-Z]/', $input)) {
                return $input;
            }
            $pattern = '/([a-z])([A-Z])/';

            return strtolower(preg_replace_callback($pattern, function ($a) {
                return $a[1].'_'.strtolower($a[2]);
            }, $input));
        };

        $tableName = $camelCaseToSnakeCase($entityName);

        if ('annotations' === $configType) {
            $entityCode = $this->getCode('EntityAnnotations.php', $entityName, $tableName);
        } else {
            $entityCode = $this->getCode('EntityAttributes.php', $entityName, $tableName);
        }
        $fileSystem = new Filesystem();

        $fileSystem->mkdir([
            $this->projectRoot.'/src/Entity/',
            $this->projectRoot.'/src/Repository/',
            $this->projectRoot.'/src/Athena/',
        ]);

        $fileSystem->dumpFile($this->projectRoot.'/src/Entity/'.$entityName.'.php', $entityCode);
        $fileSystem->dumpFile($this->projectRoot.'/src/Repository/'.$entityName.'Repository.php', $this->getCode('Repository.php', $entityName, $tableName));
        $fileSystem->dumpFile($this->projectRoot.'/src/Repository/'.$entityName.'RepositoryInterface.php', $this->getCode('RepositoryInterface.php', $entityName, $tableName));
        $fileSystem->dumpFile($this->projectRoot.'/src/Repository/'.$entityName.'ServiceRepository.php', $this->getCode('ServiceRepository.php', $entityName, $tableName));
        $fileSystem->dumpFile($this->projectRoot.'/src/Athena/'.$entityName.'Section.php', $this->getCode('AthenaSection.php', $entityName, $tableName));

        $services = $this->getCode('Services.yaml', $entityName, $tableName);
        $fileSystem->appendToFile($this->projectRoot.'/config/services.yaml', $services);

        return 0;
    }

    protected function getCode(string $file, string $className, string $tableName): string
    {
        $content = file_get_contents(dirname(__DIR__).'/Templates/'.$file.'.txt');
        $content = str_replace('{{className}}', $className, $content);
        $content = str_replace('{{tableName}}', $tableName, $content);
        $content = str_replace('{{urlTag}}', $tableName, $content);

        return $content;
    }
}
