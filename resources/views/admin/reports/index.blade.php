@extends('admin.layouts.app')
@section('title','Reports')
@section('content')
<h2 class="fw-bold mb-4">Central Reports</h2>
<form class="card p-3 mb-4"><div class="row g-2"><div class="col-md-3"><input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control"></div><div class="col-md-3"><input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control"></div><div class="col-md-2"><button class="btn btn-primary w-100">Apply</button></div></div></form>
<div class="row g-3 mb-4">@foreach([['Enquiries',$totalEnquiries],['Admissions',$totalAdmissions],['Converted / Admitted',$converted],['Conversion %',$conversionRate],['Messages Sent',$sentCommunications],['Message Failures',$failedCommunications]] as [$l,$v])<div class="col-6 col-md-4 col-xl-2"><div class="card p-3"><small class="text-muted">{{ $l }}</small><div class="h3 fw-bold mb-0">{{ $v }}</div></div></div>@endforeach</div>
<div class="card p-4"><h5>Business-wise Enquiries</h5><div class="table-responsive"><table class="table"><thead><tr><th>Business</th><th>Total</th></tr></thead><tbody>@forelse($businessRows as $row)<tr><td>{{ $row->institution?->name ?? 'MCI / Unassigned' }}</td><td>{{ $row->total }}</td></tr>@empty<tr><td colspan="2" class="text-muted">No data in selected period.</td></tr>@endforelse</tbody></table></div></div>
@endsection
