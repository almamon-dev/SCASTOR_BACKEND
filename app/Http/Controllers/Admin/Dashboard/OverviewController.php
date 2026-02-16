<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OverviewController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'projects' => [
                    'count' => 12,
                    'status' => '+ACTIVE',
                ],
                'skills' => [
                    'count' => 156,
                    'status' => '+READY',
                ],
                'experiences' => [
                    'count' => 4,
                    'status' => '+CAREER',
                ],
                'contacts' => [
                    'count' => 24,
                    'status' => '+INBOX',
                ],
            ],
            'recentProjects' => [
                [
                    'id' => 1,
                    'title' => 'Doctor Appointment & Consultation App',
                    'category' => 'API DEVELOPMENT',
                    'created_at' => 'Feb 10, 2026',
                    'initials' => 'DA',
                    'color' => 'bg-indigo-100 text-indigo-600',
                ],
                [
                    'id' => 2,
                    'title' => 'E-Commerce Platform with Stripe',
                    'category' => 'FULL STACK',
                    'created_at' => 'Feb 05, 2026',
                    'initials' => 'EC',
                    'color' => 'bg-emerald-100 text-emerald-600',
                ],
                [
                    'id' => 3,
                    'title' => 'Real-time Chat Application',
                    'category' => 'WEB SOCKET',
                    'created_at' => 'Jan 28, 2026',
                    'initials' => 'RC',
                    'color' => 'bg-purple-100 text-purple-600',
                ],
                [
                    'id' => 4,
                    'title' => 'Personal Portfolio Website',
                    'category' => 'FRONTEND',
                    'created_at' => 'Jan 15, 2026',
                    'initials' => 'PP',
                    'color' => 'bg-orange-100 text-orange-600',
                ],
                [
                    'id' => 5,
                    'title' => 'Inventory Management System',
                    'category' => 'BACKEND',
                    'created_at' => 'Jan 02, 2026',
                    'initials' => 'IM',
                    'color' => 'bg-blue-100 text-blue-600',
                ],
            ],
            'portfolioHealth' => [
                'completion' => 91,
                'activeExperiences' => 'High',
                'verifiedSkills' => 'Active',
            ],
        ]);
    }
}
