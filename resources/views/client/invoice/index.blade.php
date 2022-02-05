@extends('layouts.client')

@inject('invoice_model', 'App\Models\Invoice')
@inject('server_model', 'App\Models\Server')
@inject('tax_model', 'App\Models\Tax')

@section('title', 'Invoices')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Unpaid Invoices</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Amount</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice_model->where(['client_id' => auth()->user()->id, 'paid' => false])->get() as $invoice)
                                <tr>
                                    <td><a href="{{ route('client.invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->id }}</a></td>
                                    <td>
                                        @if ($invoice->server_id)
                                            Server #{{ $invoice->server_id }}
                                        @elseif ($invoice->credit)
                                            {!! price($invoice->credit) !!} Credit
                                        @endif
                                    </td>
                                    <td>{!! price($invoice->total) !!}</td>
                                    <td>{{ $invoice->created_at }}</td>
                                    <td>{{ $invoice->due_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Paid Invoices</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Item</th>
                                <th>Amount</th>
                                <th>Invoice Date</th>
                                <th>Paid Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice_model->where(['client_id' => auth()->user()->id, 'paid' => true])->get() as $invoice)
                                <tr>
                                    <td><a href="{{ route('client.invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->id }}</a></td>
                                    <td>
                                        @if ($invoice->server_id)
                                            Server #{{ $invoice->server_id }}
                                        @elseif ($invoice->credit)
                                            {!! price($invoice->credit) !!} Credit
                                        @endif
                                    </td>
                                    <td>{!! price($invoice->total) !!}</td>
                                    <td>{{ $invoice->created_at }}</td>
                                    <td>{{ $invoice->updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
