<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends AbstractController
{
    #[Route('/metrics', name: 'metrics_landing_page')]
    public function index(): Response
    {
        return $this->render('metrics/metrics_landing_page.html.twig');
    }
}
