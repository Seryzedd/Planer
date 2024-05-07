<?php

namespace App\Controller\Admin;

use App\Repository\AbsenceRepository;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * admin controller
 */
#[Route('/admin/absences')]
class AbsencesAdminController extends BaseController
{
    /**
     * 
     */
    #[Route('/', name: 'admin_absences_index')]
    public function index(AbsenceRepository $repository): Response
    {
        return $this->render('admin/Absence/Index.html.twig', [
            'absences' => $repository->findAllByCompany($this->getUser()->getCompany()->getId(), 'DESC')
        ]);
    }
}