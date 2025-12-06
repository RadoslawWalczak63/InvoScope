<?php

namespace App\Http\Controllers;

use App\Http\Resources\EntityResource;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EntityController extends Controller
{
    public function index(Request $request): Response
    {
        $entities = Entity::query()
            ->paginate(perPage: $request->get('per_page', 15));

        return Inertia::render('Entity/Index', [
            'entities' => EntityResource::collection($entities),
        ]);
    }
}
