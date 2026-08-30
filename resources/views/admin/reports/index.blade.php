@extends('admin.layouts.app')
@section('title','Reports')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><div><h2 class="fw-bold mb-1">Central Reports</h2><p class="text-muted mb-0">Business-wise enquiry, admission, conversion and communication performance.</p></div></div>
<form class="card p-3 mb-4"><div class="row g-2 align-items-end"><div class="col-md-3"><label class="form-label small text-muted">From</label><input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control"></div><div class="col-md-3"><label class="form-label small text-muted">To</label><input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control"></div><div class="col-md-2"><button class="btn btn-primary w-100">Apply</button></div></div></form>

<div class="row g-3 mb-4">@foreach([['Enquiries',$totalEnquiries],['Admissions',$totalAdmissions],['Converted / Admitted',$converted],['Conversion %',$conversionRate.'%'],['Messages Sent',$sentCommunications],['Message Failures',$failedCommunications],['Delivery Success',$communicationSuccessRate.'%']] as [$l,$v])<div class="col-6 col-md-4 col-xl"><div class="card p-3 h-100"><small class="text-muted text-uppercase fw-semibold">{{ $l }}</small><div class="h3 fw-bold mb-0 mt-2">{{ $v }}</div></div></div>@endforeach</div>

<div class="row g-3 mb-4">@foreach([["Today's Enquiries",$todayCount],['Last 7 Days',$sevenDayCount],['Last 30 Days',$thirtyDayCount]] as [$l,$v])<div class="col-md-4"><div class="card p-3 h-100"><small class="text-muted text-uppercase fw-semibold">{{ $l }}</small><div class="display-6 fw-bold mt-2">{{ $v }}</div></div></div>@endforeach</div>

@if($failedCommunications > 0)
<div class="alert alert-warning"><strong>Communication warning:</strong> {{ $failedCommunications }} message(s) failed in the selected period. Review Communication History for details.</div>
@endif

<div class="card p-4"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2"><div><h5 class="fw-bold mb-1">Business Performance</h5><small class="text-muted">Ranked by enquiries in the selected period.</small></div><span class="badge text-bg-light">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</span></div><div class="table-responsive mt-3"><table class="table align-middle mb-0"><thead><tr><th>#</th><th>Business</th><th>Enquiries</th><th>Admissions</th><th>Converted</th><th>Conversion %</th><th>Sent</th><th>Failed</th><th>Delivery %</th></tr></thead><tbody>@forelse($businessRows as $row)<tr><td>{{ $loop->iteration }}</td><td class="fw-semibold">{{ $row->institution?->name ?? 'MCI / Unassigned' }}</td><td>{{ $row->enquiries }}</td><td>{{ $row->admissions }}</td><td>{{ $row->converted }}</td><td>{{ $row->conversion_rate }}%</td><td>{{ $row->sent }}</td><td>{{ $row->failed }}</td><td>{{ $row->delivery_rate }}%</td></tr>@empty<tr><td colspan="9" class="text-muted text-center py-4">No business data available.</td></tr>@endforelse</tbody></table></div></div>
@endsection
