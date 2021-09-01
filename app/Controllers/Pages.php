<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Pages extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

   public function view($page = 'dashboard', $data = [])
   {
		if ( ! is_file(APPPATH.'Views/pages/'.$page.'.php'))
		{
			// Whoops, we don't have a page for that!
			throw new \CodeIgniter\Exceptions\PageNotFoundException($page);
		}

		$data['title'] = ucfirst($page); // Capitalize the first letter
    $data['page_id'] = $page;

		echo view('templates/header', $data);
    if($page != "login"){
      echo view('templates/leftMenu', $data);
    }
		echo view('pages/'.$page, $data);
		echo view('templates/footer', $data);
    }
}
