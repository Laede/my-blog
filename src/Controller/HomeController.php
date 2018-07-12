<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, PostRepository $repository): Response
    {
        $currentPage = $request->get('page',1);
        $user = $this->getUser();

        $search = $request->query->get('q');
        if($search){
            $pagerfanta = new Pagerfanta($repository->search($search));
            $pagerfanta->setMaxPerPage(5);
            $pagerfanta->setCurrentPage($currentPage);
        } else {
            $pagerfanta = new Pagerfanta($repository->orderPostsByDescOrder());
            $pagerfanta->setMaxPerPage(5);
            $pagerfanta->setCurrentPage($currentPage);
        }


        return $this->render('base.html.twig',[
            'user' => $user,
            'my_pager' => $pagerfanta
        ]);
    }
}