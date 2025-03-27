<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Console;

use App\Shared\Infrastructure\EnvironmentLoader;
use PDOException;
use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Tempest\Console\Middleware\CautionMiddleware;
use Tempest\Console\Middleware\ForceMiddleware;
use Tempest\Container\Container;
use Tempest\Container\Singleton;
use Tempest\Core\Environment;
use Tempest\Database\Config\DatabaseConfig;
use Tempest\Database\Config\MysqlConfig;
use Tempest\Database\Connection\PDOConnection;
use Tempest\Database\Exceptions\QueryException;
use Tempest\Database\GenericDatabase;
use Tempest\Database\Query;
use Tempest\Database\Transactions\TransactionManagerInitializer;

use function Tempest\env;

#[Singleton]
final readonly class SetUpTestingDatabaseCommand
{
    private const Environment ENVIRONMENT = Environment::TESTING;

    public function __construct(
        private Console $console,
        private EnvironmentLoader $environmentLoader,
        private Container $container,
        private TransactionManagerInitializer $transactionManagerInitializer,
    ) {}

    /**
     * @throws QueryException
     */
    #[ConsoleCommand(
        name: 'database:testing:create',
        description: 'It creates the testing database it and it runs the migrations on it',
        middleware: [ForceMiddleware::class, CautionMiddleware::class],
    )]
    public function __invoke(bool $force = false): ExitCode
    {
        // Load the testing environment to use the specific database environment variables to set up the
        // database connection
        $this->console->info(sprintf('Loading %s environment', self::ENVIRONMENT->value));
        $this->environmentLoader->load(self::ENVIRONMENT);

        // Set up the database connection with the loaded testing environment variables
        $mysqlConfig = $this->container->get(MysqlConfig::class);
        $this->container->singleton(DatabaseConfig::class, $mysqlConfig);

        // Create the testing database of needed
        $createdDatabase = $this->createTestingDatabase($mysqlConfig);

        $createdDatabase
            ? $this->console->success(sprintf('Created testing database: %s', $mysqlConfig->database))
            : $this->console->info(sprintf('Testing database already exists: %s', $mysqlConfig->database));

        // If the testing database has not been created and the migrations are not forced, early escape
        if (!$createdDatabase && !$force) {
            return ExitCode::SUCCESS;
        }

        // Run migrations into the testing database (we accomplish this because we have set up the database
        // configuration with the testing environment variables)
        return $this->console->call('migrate:fresh');
    }

    private function getRootMysqlConfig(MysqlConfig $mysqlConfig): MysqlConfig
    {
        return new MysqlConfig(
            host: $mysqlConfig->host,
            port: $mysqlConfig->port,
            username: 'root',
            password: env('DB_ROOT_PASSWORD'),
            database: '',
        );
    }

    /**
     * @throws QueryException
     */
    private function createTestingDatabase(MysqlConfig $mysqlConfig): bool
    {
        $rootMysqlConfig = $this->getRootMysqlConfig($mysqlConfig);
        $rootConnection = new PDOConnection($rootMysqlConfig);
        $rootConnection->connect();
        $transactionManager = $this->transactionManagerInitializer->initialize($this->container);
        $rootManagedDatabase = new GenericDatabase($rootConnection, $transactionManager);

        try {
            $transactionManager->begin();

            $rootManagedDatabase->execute(
                new Query("
                    CREATE DATABASE `$mysqlConfig->database`;
                ")
            );

            $rootManagedDatabase->execute(
                new Query("
                    GRANT ALL PRIVILEGES ON `$mysqlConfig->database`.* TO `$mysqlConfig->username`@`%`
                ")
            );

            $transactionManager->commit();

            return true;
        }
        catch (QueryException $e) {
            $transactionManager->rollback();

            /** @var PDOException $pdoException */
            $pdoException = $e->getPrevious();

            /**
             * https://www.metisdata.io/knowledgebase/errors/mysql-1007
             *
             * SQL Error 1007: Database already exists
             */
            if ($pdoException->errorInfo[1] === 1007) {
                return false;
            }

            throw $e;
        }
    }
}