<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LdapRecord\Connection;
use Illuminate\Support\Facades\Auth;

class pruebaldap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pruebaldap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = new Connection([
            'hosts'    => ['10.10.43.6'],
            'username' => 'administrator@sem132.local',
            'password' => 'pHYlabass66',
        ]);
        $user = 'administrator@sem132.local';
        $password = 'pHYlabass66';
        
        try {
            $connection->connect();
            if ($connection->auth()->attempt($user, $password)) {
                echo "Usuario y contraseña correctos      ";
            } else {
                echo "El usuario no existe o es incorrecto";
            }
            echo "Successfully connected!                  ";
        } catch (\LdapRecord\Auth\BindException $e) {
            $error = $e->getDetailedError();

            echo $error->getErrorCode();
            echo $error->getErrorMessage();
            echo $error->getDiagnosticMessage();
        }

        try {
            $query = $connection->query();
            $query->select(['cn', 'samaccountname', 'mail']);
            $record = $query->find('samaccountname=bmejia, dc=sem132, dc=local');
            if ($record) {
                echo "El usuario Existe";
            } else {
                echo "Algo salio mal";
            }
        } catch (\LdapRecord\Auth\BindException $e) {
            $error = $e->getDetailedError();

            echo $error->getErrorCode();
            echo $error->getErrorMessage();
            echo $error->getDiagnosticMessage();
        }


        try {
            $query = $connection->query();
            $credentials = [
                'mail' => 'administrator@sem132.local',
                'password' => 'pHYlabass66',
            ];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Returns true:
                $user instanceof \App\Ldap\User;
                echo "Usuario Guardado";
            } else {
                echo "Algo salio mal";
            }
        } catch (\LdapRecord\Auth\BindException $e) {
            $error = $e->getDetailedError();

            echo $error->getErrorCode();
            echo $error->getErrorMessage();
            echo $error->getDiagnosticMessage();
        }
    }
}
