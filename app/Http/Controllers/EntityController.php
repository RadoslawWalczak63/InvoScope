<?php

namespace App\Http\Controllers;

use App\Enums\EntityType;
use App\Http\Resources\EntityResource;
use App\Models\Entity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;
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
            ->allowedSorts(['type', 'country', 'email', 'phone', 'created_at'])
            ->allowedFilters([
                AllowedFilter::partial('type'),
                AllowedFilter::partial('country'),
                AllowedFilter::partial('email'),
                AllowedFilter::callback('name', function ($query, $value) {
                    $query->where(function ($query) use ($value) {
                        $query->where('company_name', 'like', "%{$value}%")
                            ->orWhere('first_name', 'like', "%{$value}%")
                            ->orWhere('last_name', 'like', "%{$value}%");
                    });
                }),
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

    public function show(Request $request, Entity $entity): Response
    {
        Gate::authorize('view', $entity);

        $entity->load('user');

        return Inertia::render('Entity/Show', [
            'entity' => new EntityResource($entity),
        ]);
    }

    public function update(Request $request, Entity $entity): RedirectResponse
    {
        Gate::authorize('update', $entity);

        $validated = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'string', new Enum(EntityType::class)],
        ]);

        $entity->update($validated);

        return redirect()
            ->route('entities.show', $entity)
            ->with('success', 'Entity updated successfully.');
    }

    public function destroy(Request $request, Entity $entity): RedirectResponse
    {
        Gate::authorize('delete', $entity);

        $entity->delete();

        return redirect()
            ->route('entities.index')
            ->with('success', 'Entity deleted successfully.');
    }
}
