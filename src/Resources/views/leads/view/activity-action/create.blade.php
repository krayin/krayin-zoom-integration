@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/zoom-meeting/assets/css/admin.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript">
        $(function() {
            $('.video-conference').append('<button type="button" class="btn btn-sm btn-secondary-outline" id="create-zoom-link"><i class="icon zoom-logo"></i>Zoom</button>');

            $('#create-zoom-link').on('click', function(e) {
                window.app.pageLoaded = false;

                window.axios.post(`{{ route('admin.zoom_meeting.create_link') }}`, {
                    }).then(response => {
                        window.app.pageLoaded = true;

                        $('input[name=location]').val(response.data.link);

                        $('#activity-comment').val(response.data.comment);

                        $('.video-conference').append('<div class="join-zoom-link"><a href="' + response.data.link + '" target="_blank" class="btn btn-sm btn-secondary-outline">Join Zoom Meeting</a><i class="icon trash-icon" id="remove-zoom-button"></i></div>');

                        $('#create-zoom-link').hide();
                    })
                    .catch(error => {
                        window.app.pageLoaded = true;

                        window.addFlashMessages({
                            type: "error",
                            message : error.response.data.message
                        });
                    });
            });

            $('.video-conference').delegate('#remove-zoom-button', 'click', function(e) {
                $('.join-zoom-link').remove();

                $('#create-zoom-link').show();

                $('input[name=location]').val('');
            });
        });
    </script>
@endpush