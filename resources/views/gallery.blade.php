@extends('layouts.app')

@section('title', 'Gallery | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom"><div class="container py-4"><span class="badge bg-success-subtle text-success mb-3">Campus Life</span><h1 class="display-5 fw-bold">Gallery</h1><p class="lead text-secondary mb-0">Photos and highlights from classrooms, events, training programs and activities across MCI Educational Group.</p></div></section>
<section class="py-5"><div class="container"><div class="row g-4">
@foreach(['Academic Activities','Student Events','Training Sessions','Campus Highlights','Workshops & Seminars','Community Programs'] as $title)
<div class="col-md-6 col-lg-4"><div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden"><div class="ratio ratio-16x9 bg-body-tertiary d-flex align-items-center justify-content-center"><div class="text-center text-secondary"><div class="display-6">📷</div><small>Gallery image</small></div></div><div class="card-body"><h2 class="h6 fw-bold mb-0">{{ $title }}</h2></div></div></div>
@endforeach
</div></div></section>
@endsection
