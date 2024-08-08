<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dreamcast Assignment</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="row">
            <div class="col-12">

                <table class="table table-striped" id="userTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role ID</th>
                            <th>Profile Image</th>
                        </tr>
                    </thead>
                    <tbody id="userdata">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h1>User Panel</h1>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" id="addUser">Add User</button>
                <button id="listUser" class="btn btn-info mx-4">List User</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mt-4">
                <div id="msg"></div>
                <form id="userForm">

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control">
                        <span id="name_error" class="error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <span id="email_error" class="error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone No</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                        <span id="phone_error" class="error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
                        <span id="description_error" class="error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="roleId" class="form-label">Role</label>
                        <select name="roleId[]" id="roleId" class="form-select" multiple>
                            @if ($roles)
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @if($role->id==2) selected @endif>{{ $role->name }}</option>
                                @endforeach
                            @endif
                        </select>

                        <span id="role_error" class="error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="file" class="form-label">Profile Image</label>
                        <input type="file" name="file" class="form-control">
                        <span id="file_error" class="error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#listUser").on('click', function() {
                $("#userForm").hide();
                document.getElementById("userForm").reset();
                $.ajax({
                    url: `{{ route('user.list') }}`,
                    type: 'GET',
                    success: function(res) {
                        let html = '';
                        if (res.data.length != 0) {
                            res.data.forEach(user => {
                                let roles = user.roles.map(role => role.name).join(
                                ', ');
                                let imageHtml = user.image ? `<img src="{{ asset('storage/images/') }}/${user.image}" width="100">` : '';
                                html += `<tr>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.phone}</td>
                                <td>${roles}</td>
                                <td>${imageHtml}</td>
                            </tr>`;
                            });
                        } else {
                            html =
                                `<tr><td colspan="5" class="text-center">Record not found</td></tr>`;
                        }
                        $('#userdata').html(html);
                        $("#userTable").show();
                    }
                });
            });


            $("#addUser").on('click', function() {
                $(".error").html("");
                $("#msg").html("");
                $("#userTable").hide();
                $("#userForm").show();
            });


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#userTable").hide();
            let form = $("#userForm");

            form.validate({
                rules: {
                    name: "required",
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true,
                        maxlength: 10,
                        minlength: 10
                    },

                },
                messages: {
                    name: "Please enter your name",
                    email: {
                        required: "Please provide an email address",
                        email: "Please enter a valid email address"
                    },
                    phone: {
                        required: "Please provide a phone number",
                        maxlength: "Please enter no more than 10 digits",
                        minlength: "Please enter a valid phone number"
                    }
                },
                submitHandler: function() {
                    let error = false;
                    let formData = new FormData(form[0]);
                    $.ajax({
                        url: `{{ route('user.create') }}`,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.status) {


                            } else {

                                if (res.message.name) {
                                    $("#name_error").html(res.message.name);
                                    error = true;
                                } else {
                                    $("#name_error").html("");
                                }
                                if (res.message.email) {
                                    $("#email_error").html(res.message.email);
                                    error = true;
                                } else {
                                    $("#email_error").html("");
                                }
                                if (res.message.phone) {
                                    $("#phone_error").html(res.message.phone);
                                    error = true;
                                } else {
                                    $("#phone_error").html("");
                                }
                                if (res.message.role_id) {
                                    $("#role_error").html(res.message.role_id);
                                    error = true;
                                } else {
                                    $("#role_error").html("");
                                }
                                if (res.message.file) {
                                    $("#file_error").html(res.message.file);
                                    error = true;
                                } else {
                                    $("#file_error").html("");
                                }


                            }
                            if (error == false) {
                                $("#msg").html(`
                                    <div class="alert alert-success">User added successfully</div>
                                    `);
                                $(".error").hide();
                                document.getElementById("userForm").reset();
                                $("#listUser").click();
                            } else {
                                $(".error").show();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });



        });
    </script>
    <script></script>

</body>

</html>
