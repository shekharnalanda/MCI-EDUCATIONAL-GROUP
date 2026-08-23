@extends('layouts.app')

@section('title', 'News & Events | MCI Educational Group')

@section('content')
<section class="page-hero py-5 bg-light border-bottom"><div class="container py-4"><span class="badge bg-primary-subtle text-primary mb-3">Updates</span><h1 class="display-5 fw-bold">News & Events</h1><p class="lead text-secondary mb-0">Latest announcements, academic updates, activities and events from MCI Educational Group and its institutions.</p></div></section>
<section class="py-5"><div class="container"><div class="row g-4">
@foreach([
['Group Announcements','Important notices and updates from MCI Educational Group will appear here.'],
['Institution Activities','Academic, training and student activities from group institutions will be highlighted here.'],
['Upcoming Events','Seminars, workshops, admissions, campaigns and special events can be published from the admin panel.']
] as $item)
<div class="col-md-6 col-lg-4"><article class="card h-100 border-0 shadow-sm rounded-4"><div class="card-body p-4"><span class="small text-success fw-semibold">MCI Update</span><h2 class="h5 fw-bold mt-2">{{ $item[0] }}</h2><p class="text-secondary mb-0">{{ $item[1] }}</p></div></article></div>
@endforeach
</div></div></section>
@endsection
