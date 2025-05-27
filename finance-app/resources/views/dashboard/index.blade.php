@extends('layouts.app')
@section('content')
<x-top-bar />
<div id="chartContainer">
  @include('dashboard.charts._chart-donut')
  @include('dashboard.charts._chart-finance')
  @include('dashboard.charts._chart-expense-category')
  @include('dashboard.charts._chart-income-category')
  @include('dashboard.charts._chart-aportes-vs-saidas')
  @include('dashboard.charts._chart-daily')
  @include('dashboard.charts._chart-evolution')
</div>
@endsection
@push('scripts')
  @include('dashboard.scripts.charts.donut')
  @include('dashboard.scripts.charts.finance')
  @include('dashboard.scripts.charts.expenses')
  @include('dashboard.scripts.charts.incomes')
  @include('dashboard.scripts.charts.aportes')
  @include('dashboard.scripts.charts.daily')
  @include('dashboard.scripts.charts.evolution')
  @include('dashboard.scripts.chart-selector')
@endpush