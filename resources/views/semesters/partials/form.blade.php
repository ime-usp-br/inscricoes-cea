<div class="row custom-form-group justify-content-center">
    <div class="col-12 col-md-6 text-md-right">
        <label for="year">Ano *</label>
    </div>
    <div class="col-12 col-md-6">
        <input class="custom-form-control" style="max-width:200px;" style="max-width:200px;" type="text" name="year" id="year"
            value='{{ $periodo->year ?? ""}}'
        />
    </div>
</div>

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-md-6 text-md-right">
        <label for="period">Período *</label>
    </div>
    <div class="col-12 col-md-6">
        <select class="custom-form-control" style="max-width:200px;" type="text" name="period"
            id="period"
        >
            <option value="" {{ ( $periodo->period) ? '' : 'selected'}}></option>

            @foreach ([
                        '1° Semestre',
                        '2° Semestre',
                     ] as $period)
                <option value="{{ $period }}" {{ ( $periodo->period === $period) ? 'selected' : ''}}>{{ $period }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-md-6 text-md-right">
        <label for="started_at">Data inicial *</label>
    </div>

    <div class="col-12 col-md-6" style="white-space: nowrap;">
        <input class="custom-form-control custom-datepicker" style="max-width:200px;"
            type="text" name="started_at" id="started_at" autocomplete="off"
            value="{{ old('started_at') ?? $periodo->started_at ?? ''}}"
        />
    </div>
</div>

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-md-6 text-md-right">
        <label for="finished_at">Data final *</label>
    </div>

    <div class="col-12 col-md-6" style="white-space: nowrap;">
        <input class="custom-form-control custom-datepicker" style="max-width:200px;"
            type="text" name="finished_at" id="finished_at" autocomplete="off"
            value="{{  old('finished_at') ?? $periodo->finished_at ?? ''}}"
        />
    </div>
</div>

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-md-6 text-md-right">
        <label for="start_date_enrollments">Data inicial das inscrições*</label>
    </div>

    <div class="col-12 col-md-6" style="white-space: nowrap;">
        <input class="custom-form-control custom-datepicker" style="max-width:200px;"
            type="text" name="start_date_enrollments" id="start_date_enrollments" autocomplete="off"
            value="{{ old('start_date_enrollments') ?? $periodo->start_date_enrollments ?? ''}}"
        />
    </div>
</div>

<div class="row custom-form-group align-items-center">
    <div class="col-12 col-md-6 text-md-right">
        <label for="end_date_student_registration">Data final das inscrições*</label>
    </div>

    <div class="col-12 col-md-6" style="white-space: nowrap;">
        <input class="custom-form-control custom-datepicker" style="max-width:200px;"
            type="text" name="end_date_enrollments" id="end_date_enrollments" autocomplete="off"
            value="{{  old('end_date_enrollments') ?? $periodo->end_date_enrollments ?? ''}}"
        />
    </div>
</div>


<div class="row custom-form-group justify-content-center">
    <div class="col-sm-6 text-center text-sm-right my-1">
        <button type="submit" class="btn btn-outline-dark">
            {{ $buttonText }}
        </button>
    </div>
    <div class="col-sm-6 text-center text-sm-left my-1">
        <a class="btn btn-outline-dark"
            href="{{ route('semesters.index') }}"
        >
            Cancelar
        </a>
    </div>
</div>