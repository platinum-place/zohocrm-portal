<?php

namespace doc;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Sesion implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = session();

        if (!$auth->get("usuario")) {
            return redirect()->to(site_url('login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
