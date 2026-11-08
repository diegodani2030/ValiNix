<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\SiteContent;

/**
 * Página principal (landing page).
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'stats'          => SiteContent::stats(),
            'problems'       => SiteContent::problems(),
            'services'       => SiteContent::services(),
            'benefits'       => SiteContent::benefits(),
            'steps'          => SiteContent::processSteps(),
            'projectFilters' => SiteContent::projectFilters(),
            'projects'       => SiteContent::projects(),
            'plans'          => SiteContent::plans(),
            'testimonials'   => SiteContent::testimonials(),
            'faqs'           => SiteContent::faqs(),
            'projectTypes'   => SiteContent::projectTypes(),
            'budgetRanges'   => SiteContent::budgetRanges(),
            'form'           => [
                'status'  => Session::pullFlash('form_status'),
                'message' => Session::pullFlash('form_message'),
                'old'     => Session::pullFlash('form_old', []),
                'token'   => Session::csrfToken(),
            ],
        ]);
    }
}
