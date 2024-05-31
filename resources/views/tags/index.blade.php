@extends('auth.layouts')

@section('content')
    <div class="alert-container">
        @if (session()->has('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div>
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endforeach
        @endif
    </div>

    <section class="tags">
        <div class="gropus">
            <div class="groups-item2">
                <div class="section-1-item">
                    <div class="section-1-logo">
                        <img alt="Logo" class="section-1-img" src="{{ asset('images/logo_wbg.png') }}">
                    </div>
                </div>
            </div>

            <div class="groups-sec">
                <div class="groups-item">
                    <div class="groups-center">
                        @if(Auth::user()->role == 'moderator' || Auth::user()->role == 'admin')
                            <h2 class="group-title">{{ __('messages.add_tag') }}</h2>
                            <form action="{{ route('tags.store') }}" method="POST" class="group-form">
                                @csrf
                                <input type="text" name="name" class="group-select" placeholder="{{ __('messages.title_tag') }}">
                                <button class="but-group" type="submit">{{ __('messages.submit') }}</button>
                            </form>
                        @endif
                        <h2 class="group-title2">{{ __('messages.all_tags') }}</h2>
                        <div class="table-wrapper">
                            <table class="fl-table" id="sortable-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.created_at') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $counter=1 @endphp
                                @foreach ($tags as $tag)
                                    @if($tag->team_id == Auth::user()->team_id)
                                        <tr>
                                            <td>{{ $counter }}</td>
                                            <td>{{ $tag->name }}</td>
                                            <td>{{ $tag->created_at }}</td>
                                            <td class="actions-container">
                                                @if(($tag->team_id == Auth::user()->team_id) && (Auth::user()->role == 'moderator' || Auth::user()->role == 'admin') )
                                                    <button class="tag-edit-btn tag-action-edit" data-id="{{ $tag->id }}">{{ __('messages.edit') }}</button>
                                                    <form action="{{ route('tags.destroy', ['tag' => $tag->id]) }}" method="POST" class="d-inline delete-tag-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="tag-action-del" onclick="confirmDelete(this)">{{ __('messages.delete') }}</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $counter++; @endphp
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- The Modal -->
    <div id="editTagModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="group-title">{{ __('messages.edit_tag') }}</h2>
            <form id="editTagForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTagId">
                <input type="text" name="name" id="editTagName" class="group-select" placeholder="{{ __('messages.tag_name') }}" required>
                <button type="submit" class="but-tag">{{ __('messages.update_tag') }}</button>
            </form>
        </div>
    </div>

    <img src="{{ asset('images/background/bg_1.png') }}" class="bg-img bg-img1">
    <img src="{{ asset('images/background/bg_2.png') }}" class="bg-img bg-img2">
    <img src="{{ asset('images/background/bg_3.png') }}" class="bg-img bg-img3">
    <img src="{{ asset('images/background/bg_4.png') }}" class="bg-img bg-img4">

    <script>
        $(document).ready(function() {
            var modal = $('#editTagModal');
            var span = $('.close');

            span.on('click', function() {
                modal.hide();
            });

            $(window).on('click', function(event) {
                if ($(event.target).is(modal)) {
                    modal.hide();
                }
            });

            $('.tag-edit-btn').on('click', function() {
                var tagId = $(this).data('id');
                $.get('/tags/' + tagId + '/edit', function(data) {
                    $('#editTagId').val(data.id);
                    $('#editTagName').val(data.name);
                    $('#editTagForm').attr('action', '/tags/' + data.id);
                    modal.show();
                });
            });
        });
        function confirmDelete(button) {
            if (confirm("{{ __('messages.confirm_delete') }}")) {
                $(button).closest('form').submit();
            }
        }
    </script>
@endsection
