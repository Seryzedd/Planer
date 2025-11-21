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
    #[Route('/', name: 'admin_absences_index', defaults: ['admin' => true, 'title' => 'Absences', 'icon' => 'mug-hot', 'role' => 'ROLE_ADMIN'])]
    public function index(AbsenceRepository $repository): Response
    {
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $absences = $repository->findBy([], ['fromDate' => 'DESC']);
        } else {
            $absences = $repository->findAllByCompany($this->getUser()->getCompany()->getId(), 'DESC');
        }
        return $this->render('admin/Absence/Index.html.twig', [
            'absences' => $absences
        ]);
    }
}