@extends('layouts.app')

@section('title', 'Downloads | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom"><div class="container py-4"><span class="badge bg-primary-subtle text-primary mb-3">Resources</span><h1 class="display-5 fw-bold">Downloads</h1><p class="lead text-secondary mb-0">Important forms, brochures, notices and learning resources can be published here for students and visitors.</p></div></section>
<section class="py-5"><div class="container"><div class="row g-4">
@foreach([
['Admission Forms','Download admission and registration related documents.'],
['Prospectus & Brochures','Official information brochures from MCI Educational Group and its institutions.'],
['Notices & Circulars','Important downloadable notices, circulars and public documents.'],
['Study Resources','Selected academic and learning resources for students.']
] as $item)
<div class="col-md-6"><div class="card h-100 border-0 shadow-sm rounded-4"><div class="card-body p-4"><h2 class="h5 fw-bold">{{ $item[0] }}</h2><p class="text-secondary mb-3">{{ $item[1] }}</p><span class="badge text-bg-light border">Files will be managed from Admin Panel</span></div></div></div>
@endforeach
</div></div></section>
@endsection
