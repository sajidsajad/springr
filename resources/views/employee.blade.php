<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Springr</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

  <div class="container mt-2">

      <div class="row">

          <div class="col-md-12 card-header text-center font-weight-bold">
            <h2>Laravel Skill Test</h2>
          </div>
          <div class="col-md-12 mt-4 mb-2">
            <h3 class="float-left">User Records</h3>
            <button type="button" id="addNewEmployee" class="float-right btn btn-success">Add New</button>
          </div>
          <div class="col-md-12">
              <table class="table border">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Avatar</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Experience</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody> 
                  @foreach ($employees as $employee)
                  <tr>
                      <td><img style="border-radius:50%;" src="/image/{{ $employee->image }}" height="50px" width="50px"></td>
                      <td >{{ $employee->full_name }}</td>
                      <td>{{ $employee->email }}</td>
                      <td>{{ $employee->experience }}</td>
                      <td>
                        <a href="javascript:void(0)" class="btn btn-danger delete" data-id="{{ $employee->id }}">Delete</a>
                      </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
              {!! $employees->links() !!}
          </div>
      </div>        
  </div>

                      <!-- boostrap model -->
  <div class="modal fade" id="ajax-employee-modal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="ajaxEmployeeModal"></h4>
        </div>
        <div class="modal-body">
          
          <form action="javascript:void(0)" enctype="multipart/form-data" id="addEditEmployeeForm" name="addEditEmployeeForm" class="form-horizontal" method="POST">
            <input type="hidden" name="id" id="id">
            <div class="form-group row">
              <label for="email" class="col-sm-3 control-label">Email</label>
              <div class="col-sm-7">
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" value="" required="">
              </div>
            </div>  

            <div class="form-group row">
              <label for="fullName" class="col-sm-3 control-label">Full Name</label>
              <div class="col-sm-7">
                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Enter your full name" value="" maxlength="50" required="">
              </div>
            </div>

            <div class="form-group row">
              <label for="dateOfJoining" class="col-sm-3 control-label">Date of Joining</label>
              <div class="col-sm-7">
              <!-- <input type="date" id="birthday" name="birthday"> -->
                <input type="date" class="form-control" id="dateOfJoining" name="dateOfJoining" required="">
              </div>
            </div>  

            <div class="form-group row">
              <label for="dateOfLeaving" class="col-sm-3 control-label">Date of Leaving</label>
              <div class="col-sm-7" style="display: -webkit-box;">
                <input type="date" class="form-control col-sm-6" id="dateOfLeaving" name="dateOfLeaving">
                <input class="form-check-inline col-sm-6" type="checkbox" style="height: 32px;width: 50px;" value="" id="flexCheckChecked"/>
                <label class="form-check-label" style="margin-left: 10px;" for="flexCheckChecked">Still working</label>
              </div>
            </div>

            <div class="form-group row">
              <label for="image" class="col-sm-3 control-label">Upload Image</label>
              <div class="col-sm-7">
              <input type="file" id="image" name="image" class="form-control" placeholder="image">
              </div>
            </div>

            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-primary" id="btn-save" value="addNewEmployee">Save changes
              </button>
            </div>
          </form>
        </div>
        <div class="modal-footer">          
        </div>
      </div>
    </div>
  </div>
                      <!-- end bootstrap model -->
    <script type="text/javascript">
    $(document).ready(function($){

        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#addNewEmployee').click(function () {
          $('#addEditEmployeeForm').trigger("reset");
          $('#ajaxEmployeeModal').html("Add Employee");
          $('#ajax-employee-modal').modal('show');
        });
    

        $('body').on('click', '.delete', function () {

          if (confirm("Delete Record?") == true) {
            let id = $(this).data('id');
            
            $.ajax({
                type:"POST",
                url: "{{ url('api/delete-employee') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){

                  window.location.reload();
              }
            });
          }

        });


        //  add employee
        
        // $('body').on('click', '#btn-save', function (event) {
        // $('form').on('submit', '#btn-save', function (event) {
            $( 'form' ).submit(function ( event ) {

            //   var id = $("#id").val();
            //   var email = $("#email").val();
            //   var fullName = $("#fullName").val();
            //   var dateOfJoining = $("#dateOfJoining").val();
            //   var image = $("#image").value;
            // //   var image = fullPath.split("\\").pop();
            // //   console.log(fullPath);
            //   console.log(image);
            event.preventDefault();
            var formData = new FormData(this);
            console.log(formData);
              $("#btn-save").html('Please Wait...');
              $("#btn-save").attr("disabled", true);
            
            // ajax
            $.ajax({
                type:"POST",
                url: "{{ url('api/employee') }}",
                // data: {
                //   id:id,
                //   email:email,
                //   fullName:fullName,
                //   dateOfJoining:dateOfJoining,
                //   image:image,
                // },
                data: formData,
                dataType: 'json',
                success: function(res){
                window.location.reload();
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
              }
            });

        });

    });
  </script>
</body>
</html>