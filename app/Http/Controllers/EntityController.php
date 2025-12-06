<?php

namespace App\Http\Controllers;

use App\Enums\Enum\EntityType;
use App\Http\Resources\EntityResource;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EntityController extends Controller
{
    public function index(Request $request): Response
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
        ]);

        $entities = QueryBuilder::for(Entity::class)
            ->defaultSort('-created_at')
            ->allowedSorts(['type', 'company_name', 'country', 'email', 'phone', 'created_at'])
            ->allowedFilters([
                AllowedFilter::partial('type'),
                AllowedFilter::partial('company_name'),
                AllowedFilter::partial('country'),
                AllowedFilter::partial('email'),
            ])
            ->where('user_id', $request->user()->id)
            ->paginate($request->input('per_page', 20))
            ->withQueryString();

        return Inertia::render('Entity/Index', [
            'entities' => EntityResource::collection($entities),
            'entityTypes' => EntityType::cases(),
            'state' => [
                'filters' => $request->input('filter', []),
                'sort' => $request->input('sort', '-created_at'),
            ],
        ]);
    }
}
