@extends('layouts.app')

@section('content')
    <div class="relative flex items-top justify-center py-4 overlay-bg">
        <div class="card shadow p-2 prediction-card col-lg-6 col-md-6">
            <div class="card-body">
            <h3 class="text-center">Prediction</h3>
            <fieldset>
                <legend class="w-auto"><h5>Images</h5></legend>
                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Company</label>
                    <input type="text" class="form-control" name="company">
                </div>

                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Image</label>
                    <input type="file" class="" name="image">
                </div>

                <div class="form-group form-inline">
                    <label class="col-sm-2 text-left p-0">Format</label>
                    <select class="form-control" name="format">
                        <option value=""> Select Output Format</option>
                        <option value="csv">CSV</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
            </fieldset>
            </div>
        </div>
    </div>
@endsection