@extends('auth.layouts')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-lg-6">
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
    </div>



    <section class="groups">
        <div class="gropus">
            <div class="groups-item2">
                <div class="section-1-item">
                    <div class="section-1-logo">
                        <img alt="Logo" class="section-1-img" src="{{ asset('images/todo/header/to_do_3.png') }}">
                    </div>
                </div>
            </div>

            <div class="groups-sec">
                <div class="groups-item">
                    <div class="groups-center">
                        <h2 class="group-title">Add group</h2>
                         <form action="{{ route('groups.store') }}" method="POST" class="group-form" >
                             @csrf
                             <input type="text" name="name" class="group-select" PLACEHOLDER="Title group">
                             <button class="but-group" type="submit">Submit</button>
                         </form>

                        <script>
                            $(document).ready(function(){
                                $('#sortable-table tbody').sortable({
                                    axis: 'y',
                                    update: function (event, ui){
                                        var groupId = $("#sortable-table tbody tr").map(function () {
                                            return $(this).data("group-id");
                                        }).get();

                                        $.ajax({
                                            type: "POST",
                                            url: "{{ route("groups.reorder") }}",
                                            data:
                                                {
                                                    groupId: groupId
                                                },
                                            success: function(data){
                                                console.log("Order updated successfully");
                                            },
                                            error: function(error){
                                                console.log("Error updating order: " +error);
                                            }
                                        });
                                    }
                                });
                                $("#sortable-table tbody").disableSelection();
                            });
                        </script>

                        <h2 class="group-title2">All Groups</h2>

                        <div class="table-wrapper">
                                <table class="fl-table" id="sortable-table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Created at</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @php $counter=1 @endphp
                                    @foreach ($groups as $group)
                                        <tr>
                                            <td>{{ $counter }}</td>
                                            <td>{{ $group->name }}</td>
                                            <td>{{ $group->created_at }}</td>
                                            <td>
                                                <a href="{{ route('groups.edit', ['group' => $group->id]) }}" class="group-act-edit group-luke">Edit</a>
                                                <a href="{{ route('groups.destroy', ['group' => $group->id] )}}" class="group-act-del">Delete</a>

                                            </td>
                                        </tr>
                                        @php $counter++; @endphp
                                    @endforeach
                                    </tbody>
                                </table>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
