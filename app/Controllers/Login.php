<?php

namespace App\Controllers;

use App\Libraries\Zoho;
use App\Libraries\ZohoClient;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use CodeIgniter\HTTP\RedirectResponse;
use zcrmsdk\crm\exception\ZCRMException;

class Login extends BaseController
{
    protected $zoho;

    public function __construct()
    {
        $this->zoho = new ZohoClient();
    }

    public function index()
    {
        return view('login/index');
    }

    public function login()
    {
        $model = new User();
        $user = $model->where('username', $this->request->getPost('username'))->first();

        if ($user && password_verify($this->request->getPost('password'), $user['password'])) {
            $relation_model = new CompanyUser();
            $relation = $relation_model->where('user_id', $user['id'])->first();

            $company_model = new Company();
            $company = $company_model->where('id', $relation['company_id'])->first();

            $session = session();

            $session->set([
                'user_id' => $user['id'],
                'name' => $user['name'],
                'zoho_id' => $user['zoho_id'],
                'zoho_company_id' => $company['zoho_id'],
                'company_name' => '',
                'is_admin' =>$user['is_admin']
            ]);

            if ($user['is_admin']) {
                $session->setFlashdata('alert', 'Has iniciado sesión como administrador. Podrás visualizar las cotizaciones y emisiones de los demás usuarios.');
            }

            return redirect()->to('/');
        }

        return redirect()->back()->with('alert', 'Credenciales inválidas.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    public function index2()
    {
        $libreria = new Zoho;

        if ($this->request->getPost()) {

            $libreria = new Zoho;
            //buscar el todos los usuarios con el correo y contraseña sean iguales
            //los correos son campos unicos en el crm
            $criteria = "((Email:equals:" . $this->request->getPost("correo") . ") and (Contrase_a:equals:" . $this->request->getPost("pass") . "))";
            $usuarios = $libreria->searchRecordsByCriteria("Contacts", $criteria, 1, 1);
            var_dump($usuarios);
            //buscar el todos los usuarios con el correo y contraseña sean iguales
            //los correos son campos unicos en el crm
            foreach ((array)$usuarios as $usuario) {
                //el objeto con las propiedades de la api pasa a ser una sesion
                session()->set('cuenta', $usuario->getFieldValue('Account_Name')->getLookupLabel());
                session()->set('cuenta_id', $usuario->getFieldValue('Account_Name')->getEntityId());
                session()->set('usuario', $usuario->getFieldValue('First_Name') . " " . $usuario->getFieldValue('Last_Name'));
                session()->set('usuario_id', $usuario->getEntityId());

                if ($usuario->getFieldValue("Title") == "Administrador") {
                    session()->set('admin', true);
                    session()->setFlashdata('alerta', 'Has iniciado sesión como administrador. Podrás visualizar las cotizaciones y emisiones de los demás usuarios.');
                }

                return redirect()->to(site_url());
            }

            //alerta que dara en caso de no encontrar ningun resultado
            session()->setFlashdata('alerta', 'Usuario o contraseña incorrectos.');
            return redirect()->to(site_url('login'));
        }

        return view('login');
    }

    /**
     * @throws ZCRMException
     */
    public function editar(): RedirectResponse
    {
        $libreria = new Zoho;
        $libreria->update("Contacts", session('usuario_id'), ["Contrase_a" => $this->request->getPost("pass")]);
        //alerta
        session()->setFlashdata('alerta', 'La contraseña ha sido actualizada.');
        //recargar la pagina para limpiar el post
        return redirect()->to(site_url());
    }

    public function salir(): RedirectResponse
    {
        //eliminar todas las sesiones
        session()->destroy();
        //redirigir al login
        return redirect()->to(site_url("login"));
    }
}
