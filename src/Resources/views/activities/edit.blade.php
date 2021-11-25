@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/zoom-meeting/assets/css/admin.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript">
        $(function() {
            var account = @json(app('\Webkul\ZoomMeeting\Repositories\AccountRepository')->findOneByField('user_id', auth()->user()->id));

            if (! account) {
                $('.video-conference').append('<a href="{{ route('admin.zoom_meeting.index') }}" target="_blank" class="btn btn-sm btn-secondary-outline connect-account" id="connect-zoom-account"><i class="icon zoom-logo"></i>Connect Zoom Account</a>');
            } else {
                var activity = @json($activity);

                if (activity.location && activity.location.includes('zoom')) {
                    $('.video-conference').append('<button type="button" class="btn btn-sm btn-secondary-outline create-link" id="create-zoom-link" style="display: none"><i class="icon zoom-logo"></i>Zoom Meet Meeting</button>');

                    $('.video-conference').append('<span class="join-zoom-link join-link"><a href="' + activity.location + '" target="_blank" class="btn btn-sm btn-secondary-outline">Join Zoom Meeting</a><i class="icon trash-icon" id="remove-zoom-button"></i></span>');
                } else {
                    $('.video-conference').append('<button type="button" class="btn btn-sm btn-secondary-outline create-link" id="create-zoom-link"><i class="icon zoom-logo"></i>Zoom</button>');
                }

                $('#create-zoom-link').on('click', function(e) {
                    window.app.pageLoaded = false;

                    var formElement = $('.video-conference').parents('form');

                    window.axios.post(`{{ route('admin.zoom_meeting.create_link') }}`, {
                        'title': formElement.find('input[name="title"]').val(),
                        'schedule_from': formElement.find('input[name="schedule_from"]').val(),
                        'schedule_to': formElement.find('input[name="schedule_to"]').val(),
                        'participants': {
                            'users': $("input[name='participants[users][]").map(function(){return $(this).val();}).get(),
                            'persons': $("input[name='participants[persons][]").map(function(){return $(this).val();}).get(),
                        }
                    }).then(response => {
                        window.app.pageLoaded = true;

                        $('input[name=location]').val(response.data.link);

                        $('#activity-comment').val(response.data.comment);

                        $('.video-conference').append('<span class="join-zoom-link join-link"><a href="' + response.data.link + '" target="_blank" class="btn btn-sm btn-secondary-outline">Join Zoom Meeting</a><i class="icon trash-icon" id="remove-zoom-button"></i></span>');

                        $('.create-link').hide();

                        $('.connect-account').hide();
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
                    $('.join-link').remove();

                    $('.create-link').show();

                    $('.connect-account').show();

                    $('input[name=location]').val('');
                });
            }
        });
    </script>
@endpush