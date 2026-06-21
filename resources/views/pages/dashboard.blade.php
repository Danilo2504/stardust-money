@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="m-0">Resumen</h4>
            <button type="button"
                    class="btn btn-accent"
                    data-bs-toggle="modal"
                    data-bs-target="#expenseModal">
                <i class="bi bi-plus-lg me-1"></i> Registrar gasto
            </button>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card card-custom balance-card p-4">
                    <h6>Balance total</h6>
                    <h2>€12,450</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom p-4 highlight-red">
                    <h6>Última transferencia</h6>
                    <p>- €250 a Juan</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 highlight-blue">
                    <h6>Ingresos</h6>
                    <p>€5,200</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 highlight-red">
                    <h6>Gastos</h6>
                    <p>€2,100</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3">
                    <h6>Ahorros</h6>
                    <p>€3,800</p>
                </div>
            </div>
        </div>
    </div>

    @include('pages.expenses.form')
@endsection