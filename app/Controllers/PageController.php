<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\SiteContent;

/**
 * Páginas informativas del sitio: una acción por página del menú.
 */
final class PageController extends Controller
{
    /** GET / */
    public function home(): void
    {
        $this->page('inicio', 'pages/home', [
            'stats'        => SiteContent::stats(),
            'problems'     => SiteContent::problems(),
            'explore'      => SiteContent::explorePages(),
            'testimonials' => SiteContent::testimonials(),
        ]);
    }

    /** GET /servicios */
    public function services(): void
    {
        $this->page('servicios', 'pages/services', [
            'services' => SiteContent::services(),
            'benefits' => SiteContent::benefits(),
        ]);
    }

    /** GET /proceso */
    public function process(): void
    {
        $this->page('proceso', 'pages/process', [
            'steps' => SiteContent::processSteps(),
        ]);
    }

    /** GET /proyectos */
    public function projects(): void
    {
        $this->page('proyectos', 'pages/projects', [
            'projectFilters' => SiteContent::projectFilters(),
            'projects'       => SiteContent::projects(),
        ]);
    }

    /** GET /precios */
    public function pricing(): void
    {
        $this->page('precios', 'pages/pricing', [
            'plans' => SiteContent::plans(),
        ]);
    }

    /** GET /preguntas-frecuentes */
    public function faq(): void
    {
        $this->page('faq', 'pages/faq', [
            'faqs' => SiteContent::faqs(),
        ]);
    }

    /** @param array<string, mixed> $data */
    private function page(string $key, string $view, array $data): void
    {
        $this->view($view, ['page' => SiteContent::page($key)] + $data);
    }
}
