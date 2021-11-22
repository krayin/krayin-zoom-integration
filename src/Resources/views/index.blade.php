@extends('admin::layouts.master')

@section('page_title')
    {{ __('zoom_meeting::app.title') }}
@stop

@push('css')
    <style>
        #options > div {
            padding: 10px;
            box-shadow: rgb(0 0 0 / 24%) 0px 3px 8px;
        }
    </style>
@endpush

@section('content-wrapper')
    <div class="content full-page adjacent-center">
        {!! view_render_event('admin.zoom_meeting.header.before') !!}

        <div class="page-header">

            <div class="page-title">
                <h1>{{ __('zoom_meeting::app.title') }}</h1>
            </div>
        </div>

        {!! view_render_event('admin.zoom_meeting.calendar.header.after') !!}

        <div class="page-content">
            <div class="form-container">

                <div class="panel">
                    <div class="panel-header">
                        {!! view_render_event('admin.zoom_meeting.calendar.form_buttons.before') !!}

                        <a href="{{ route('admin.settings.attributes.index') }}">{{ __('zoom_meeting::app.back') }}</a>

                        {!! view_render_event('admin.zoom_meeting.calendar.form_buttons.after') !!}
                    </div>

                    @if ($account)
                        <div class="tabs-content configure-zoom">
                            <div class="header">
                                <form method="POST" action="{{ route('admin.zoom_meeting.destroy', $account->id) }}">
                                    @csrf()

                                    <input name="_method" type="hidden" value="DELETE">

                                    <input name="route" type="hidden" value="meet">

                                    <div class="icon-container">
                                        <span class="zoom-logo"></span>
                                    </div>

                                    <div class="title">
                                        <span>{{ __('zoom_meeting::app.title') }}</span>

                                        <p>{{ __('zoom_meeting::app.zoom-meeting-info') }}</p>

                                        <button type="submit" onclick="return confirm('{{ __('zoom_meeting::app.confirm-remove') }}')">{{ __('zoom_meeting::app.remove') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="tabs-content connect-zoom">
                            <a href="{{ route('admin.zoom_meeting.store') }}" class="connect-zoom-btn">
                                <div class="icon-container">
                                    <span class="zoom-logo"></span>
                                </div>

                                <div class="title">
                                    <span>{{ __('zoom_meeting::app.connect-zoom-meeting') }}</span>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop