@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-tachometer-alt text-blue-600"></i> Dashboard
    </h1>
    <div class="bg-gray-200 px-4 py-2 rounded-lg">
        <i class="fas fa-building text-gray-600"></i>
        <span class="ml-2 text-gray-700">{{ Auth::user()->agency->name ?? 'Agence' }}</span>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Carte Pages -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-blue-100 text-sm">Pages</p>
                <p class="text-3xl font-bold">{{ $stats['pages']['total'] ?? 0 }}</p>
                <p class="text-xs text-blue-100 mt-2">
                    Publiées: {{ $stats['pages']['published'] ?? 0 }} | Brouillons: {{ $stats['pages']['draft'] ?? 0 }}
                </p>
            </div>
            <i class="fas fa-file-alt text-4xl text-blue-200"></i>
        </div>
        <a href="{{ route('pages.index') }}" class="mt-4 inline-block text-sm text-blue-100 hover:text-white">
            Gérer <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Carte Destinations -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-green-100 text-sm">Destinations</p>
                <p class="text-3xl font-bold">{{ $stats['destinations']['total'] ?? 0 }}</p>
                <p class="text-xs text-green-100 mt-2">Mises en avant: {{ $stats['destinations']['featured'] ?? 0 }}</p>
            </div>
            <i class="fas fa-map-marker-alt text-4xl text-green-200"></i>
        </div>
        <a href="#" class="mt-4 inline-block text-sm text-green-100 hover:text-white">
            Gérer <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Carte Offres -->
    <div class="bg-gradient-to-r from-teal-500 to-teal-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-teal-100 text-sm">Offres</p>
                <p class="text-3xl font-bold">{{ $stats['offers']['total'] ?? 0 }}</p>
                <p class="text-xs text-teal-100 mt-2">Offres spéciales: {{ $stats['offers']['special'] ?? 0 }}</p>
            </div>
            <i class="fas fa-tags text-4xl text-teal-200"></i>
        </div>
        <a href="#" class="mt-4 inline-block text-sm text-teal-100 hover:text-white">
            Gérer <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    <!-- Carte Utilisateurs -->
    <div class="bg-gradient-to-r from-gray-700 to-gray-800 rounded-xl shadow-lg p-6 text-white">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-300 text-sm">Utilisateurs</p>
                <p class="text-3xl font-bold">{{ $stats['users']['total'] ?? 0 }}</p>
                <p class="text-xs text-gray-300 mt-2">Administrateurs: {{ $stats['users']['admins'] ?? 0 }}</p>
            </div>
            <i class="fas fa-users text-4xl text-gray-400"></i>
        </div>
        <a href="#" class="mt-4 inline-block text-sm text-gray-300 hover:text-white">
            Gérer <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Dernières pages -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gray-800 px-6 py-4">
            <h2 class="text-white font-semibold">
                <i class="fas fa-history mr-2"></i> Dernières pages modifiées
            </h2>
        </div>
        <div class="p-6">
            @if(isset($recentPages) && $recentPages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Titre</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Statut</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Modifiée</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPages as $page)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $page->title }}</td>
                                <td class="px-4 py-3">
                                    @if($page->status === 'published')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Publiée</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Brouillon</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $page->updated_at->diffForHumans() }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('pages.edit', $page) }}" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucune page pour le moment.</p>
            @endif
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gray-800 px-6 py-4">
            <h2 class="text-white font-semibold">
                <i class="fas fa-bolt mr-2"></i> Actions rapides
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('pages.create') }}" class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus-circle mr-2"></i> Créer une page
                </a>
                <a href="#" class="block w-full bg-green-600 text-white text-center py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter une destination
                </a>
                <a href="#" class="block w-full bg-teal-600 text-white text-center py-2 rounded-lg hover:bg-teal-700 transition">
                    <i class="fas fa-plus-circle mr-2"></i> Ajouter une offre
                </a>
            </div>
        </div>
    </div>
</div>
@endsection